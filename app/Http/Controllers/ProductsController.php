<?php

namespace App\Http\Controllers;

use App\Models\Counters;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Symfony\Component\Intl\Currencies;
use App\Models\CmsPagesContent;
use App\Http\Controllers\AppController;

class ProductsController extends Controller
{
    /**
     * Show Products page
     *
     * @return \Illuminate\Http\Response
     */


    public function product_per_page(){
        $default_per_page = 15;
        $productPagesContent = CmsPagesContent::where('field_slug',"=",'product_per_page')->first();
        if($productPagesContent){
            $default_per_page = $productPagesContent->field_value;
        }

        return $default_per_page;
    }

    public function index()
    {

        $shop = Auth::user();
        $store = $shop->name;



        $charge = DB::table('charges')->where('user_id',$shop->id)->where('status',"ACTIVE")->orderBy('created_at', 'desc')->first();
        if( $charge ){
            $counters = Counters::where('user_id', $shop->id)->where('charge_id', $charge->id)->where('status', 'active')->first();
            $plan = DB::table('plans')->where('id', $shop->plan_id)->first();
            $counter['current_plan'] = $plan->name;
            $counter['total_product'] = ($counters) ? $counters->regular_product_count : 0;
            $counter['total_auto_product'] = ($counters) ? $counters->auto_update_count + $counters->auto_update_affiliate : 0;
            $counter['total_affiliate_product'] = ($counters) ? $counters->affiliate_product_count : 0;
        }

        return view('products', compact('counter', 'store'));
    }

    /**
     * Show Products page
     *
     * @return \Illuminate\Http\Response
     */
    public function getProducts(Request $request)
    {
        $shop = Auth::user();
        $shopURL = 'https://' . $shop->getDomain()->toNative();

      //  $this->updateCollections();

        $errors = [];

        try {

            $input = $request->all();

            $filterData = $input['filterData'];

            $is_pack = $request->is_pack;

            $perPageNumber = $this->product_per_page();

            logger("product per page");

            logger($perPageNumber);

            // dd(DB::table('products')->where('user_id', $shop->id)->count());
            $productRecsQuery = DB::table('products')->where('user_id', $shop->id);

            //Filter :: Start =========================================================================================

            //filter For collection
            if(isset($filterData['collection']) && $filterData['collection']!=="0"){
                $filter_by_collection = $filterData['collection'];

                $CollectProductsData = $this->GetAllAssignCollectionProducts($filter_by_collection);

                //logger("CollectProductsData");
                //logger(json_encode($CollectProductsData));

                $productRecsQuery->whereIn('products.shopify_id', $CollectProductsData);

            }

            //filter For search title, description, variant name
            if(isset($filterData['search']) && $filterData['search']!=="" && $filterData['search'] !==null){
                $filter_by_search = $filterData['search'];

               $db_variants_product = DB::table('variants')->where('title', 'LIKE', "%$filter_by_search%")->where('is_main',1)->pluck('product_id');

               $productRecsQuery->whereIn('products.id', $db_variants_product);
                                 //->orWhere('products.description', 'LIKE', "%$filter_by_search%")->groupBy('variants.product_id');
            }

            //filter For Tags
            if(isset($filterData['tag']) && $filterData['tag']!=="0"){
                $filter_by_tag = $filterData['tag'];

                //logger("filter by tags=> ".$filter_by_tag);

                $productRecsQuery->join('tags', 'products.id', '=', 'tags.product_id');
                $productRecsQuery->where('tags.tag','=', $filter_by_tag);

            }


            //Filter :: End ============================================================================================

            $productRecsQuery->select('products.*');
            $productRecs =  $productRecsQuery->orderBy('products.id', 'desc')->paginate($perPageNumber);

        } catch (QueryException $e) {
            $errors[] = $e->getMessage();
            return ['success' => false, 'errors' => $errors];
        }

        $shoppifyIDArray = [];
        $products = [];
        $shopifyProducts = [];

            for ($p = 0; $p < count($productRecs); $p++) {
                $shopifyRec = [];
                    // Get product variants

                if(strtotime($productRecs[$p]->updated_at) > strtotime($productRecs[$p]->created_at)) {
                    $updatedAt = date('F/j/Y h:i A', strtotime($productRecs[$p]->updated_at));
                }else{
                    $updatedAt = "";
                }

                $reviewStatus = (@json_decode($productRecs[$p]->reviews)->status) ? json_decode($productRecs[$p]->reviews)->status : 'No';

                $db_variants = DB::table('variants')->where('product_id',$productRecs[$p]->id)->where('is_main',1)->first();

                $max_variants = [];
                $db_variants_all = DB::table('variants')->where('product_id',$productRecs[$p]->id)->get();

                $max_source_price = 0;
                $max_price = 0;

                if($db_variants_all && count($db_variants_all)>1){

                    $max_variants = $db_variants_all[count($db_variants_all)-1];

                  //  logger("max_variants");
                   // logger(json_encode($max_variants));

                    $max_source_price = $max_variants->source_price;
                    $max_price = $max_variants->shopify_price;

                }

                $db_variants_count = 0;

                if($productRecs[$p]->source=="Amazon"){
                    $db_variants_count = DB::table('variants')->where('product_id',$productRecs[$p]->id)->where('is_main','<>',1)->count();
                }else{
                    $db_variants_count = count($db_variants_all);
                }

                $image_url = '';
                if($db_variants) {
                    $db_image = DB::table('images')->select('url')->where('id', $db_variants->image_id)->first();
                    $image_url = ($db_image) ? $db_image->url : '';
                }

                    $products[$p] = [
                        'id' => $productRecs[$p]->id,
                        'shopify_id' => $productRecs[$p]->shopify_id,
                        'title' => (@$productRecs[$p]->title) ? $productRecs[$p]->title : (@$db_variants->title ? $db_variants->title : ''),
                        'source' => $productRecs[$p]->source,
                        'source_url' => $productRecs[$p]->source_url,
                        'locale' => $productRecs[$p]->locale,
                        'shipping_type' => (@$productRecs[$p]->shipping_type) ? $productRecs[$p]->shipping_type : (@$db_variants->shipping_type ? $db_variants->shipping_type : "Standard shipping"),
                        'associates' => json_decode($productRecs[$p]->affiliate),
                        'updated_at' => $updatedAt,
                        'source_prices' => [],
                        'variants_count' => (@$db_variants_count) ? $db_variants_count : 0,
                        'image_url' => $image_url,
                        'product_url' => $shopURL.'/products/'.$productRecs[$p]->shopify_handle,
                        'edit_url' => $shopURL.'/admin/products/'.$productRecs[$p]->shopify_id,
                        'saller_ranks' => json_decode($productRecs[$p]->saller_ranks),
                        'min_source_price' => (@$db_variants->source_price) ? $db_variants->source_price : 0,
                        'max_source_price' => $max_source_price,
                        'min_price' => (@$db_variants->shopify_price) ? $db_variants->shopify_price : 0,
                        'max_price' => $max_price,
                        'autoupdate' => json_decode($productRecs[$p]->auto_update),
                        'review_status' => $reviewStatus,
                        'is_show_front' => (@json_decode($productRecs[$p]->reviews)->is_show_front) ? json_decode($productRecs[$p]->reviews)->is_show_front : false
                    ];

                    if ($productRecs[$p]->source == 'Amazon' && $db_variants->prime_eligible) {
                        $products[$p]['shipping_type'] = 'Prime - 3 Days';
                    }
              }

        // fetch native store currency

        $shope_data = getShopData($shop->id);

        if(!$shope_data){
            $shope_data = getShopDataFromAPI($shop, 'id, email, name,currency');
        }

        if($shope_data->currency){
            $currency = Currencies::getSymbol($shope_data->currency);
        }

        return ['success' => true, 'errors' => $errors, 'productsPagination' => $productRecs,'products' => $products,'currency'=> $currency];
    }


    public function GetAllAssignCollectionProducts($collectionID)
    {

        try {
           // logger("Get List of All Products are Assign in shopify collection");
           // logger("Filter_Collection_id".$collectionID);

            $shop = Auth::user();
            $user_id = $shop->id;

            $CollectProducts = [];

            $Result = $shop->api()->rest('GET', '/admin/api/'.env('SHOPIFY_API_VERSION').'/collects.json');


            if (!$Result->errors) {

                //logger("============Result=================");

                if (@$Result->body->collects && count($Result->body->collects) > 0) {

                    $collects = $Result->body->collects;


                    foreach ($collects as $key => $val) {

                        $sh_productID = $val->product_id;
                        $sh_collectionID = $val->collection_id;

                        if ($sh_collectionID == $collectionID) {
                            $CollectProducts[] = $sh_productID;
                        }
                    }
                }
            }


            return $CollectProducts;
        }catch( \Exception $e ){

            return ['success' => false, 'errors' => $e];

            }

    }



    public function updateCollections(){

        $shop = Auth::user();
        $user_id = $shop->id;

        $Result = $shop->api()->rest('GET', '/admin/api/'. env('SHOPIFY_API_VERSION') .'/collects.json');

       // logger(json_encode($Result));

        if(!$Result->errors) {

           // logger("============Result=================");

            if(@$Result->body->collects && count($Result->body->collects)>0){

                $collects = $Result->body->collects;
               // logger($collects);

                foreach ($collects as $key=>$val) {

                    $sh_productID = $val->product_id;

                    $category = DB::table('products')->where('shopify_id', $sh_productID)->where('user_id',
                        $user_id)->value('collection_id');


                   // logger("category");
                   // logger($category);

                    $collection_ids = [];
                    if ($category) {

                        $row = $category;

                        $row = json_decode($row, true);

                        if (is_array($row)) {

                          //  logger("is array");
                            if (!in_array($val->collection_id, $row)) {
                                array_push($row, $val->collection_id);
                            }

                            $collection_ids = $row;

                        } else {
                           // logger("is string semicoprated");

                            $idsArr = explode(',', $row);
                            if (count($idsArr) > 0) {

                                if (!in_array($val->collection_id, $idsArr)) {
                                    array_push($idsArr, $val->collection_id);
                                }
                            }

                            $collection_ids = $idsArr;
                        }

                       // logger("=============collection_ids=============");
                       // logger($collection_ids);


                        DB::table('products')->where('shopify_id', $sh_productID)->where('user_id', $user_id)
                            ->update(['collection_id' => $collection_ids, 'updated_at' => date('Y-m-d H:i:s')]);

                    }
                }
            }

        }
    }


    public function getProductsVariants(Request $request){

        $shop = Auth::user();
        $shopURL = 'https://' . $shop->getDomain()->toNative();
        $errors = [];
        try {

            $input = $request->all();
            $product_id = $input['product_id'];
            $shopify_id = $input['shopify_id'];

           // logger("Get Variants :: product_id :: ".$product_id);

            $shopifyRec = [];

            $productReq = $shop->api()->rest('GET', '/admin/api/'. env('SHOPIFY_API_VERSION') .'/products/'.$shopify_id.'.json');

           // logger("RES :: ".json_encode($productReq));
            if (!$productReq->errors) {
                if ($productReq->body->product) {
                    $shopifyProducts = $productReq->body->product;

                        $shopifyRec = $shopifyProducts;
                        if( !empty($shopifyRec) ) {
                            $variantRecs = DB::table('variants')->where('product_id', $product_id)->get();

                            // get variant image
                            $shVariant = $shopifyRec->variants;
                            if (!empty($shVariant)) {
                                foreach ($shVariant as $shvk => $shvv) {
                                    $shsku = $shvv->sku;
                                    if (!empty($variantRecs)) {
                                        foreach ($variantRecs as $dbvk => $dbvv) {
                                            if ($shsku == $dbvv->source_id) {
                                                $db_image = DB::table('images')->select('url')->where('id',
                                                    $dbvv->image_id)->first();
                                                $shvv->db_image = ($db_image) ? $db_image->url : '';
                                            }
                                        }
                                    }
                                }
                            }
                        }
                }
            }
            $attributesFinal = [];

            if(isset($variantRecs) && count($variantRecs)>0) {



                foreach ($variantRecs as $vk => $vv) {
                    $attributes = DB::table('attributes')->where('variant_id', $vv->id)->get();
                    if ($attributes->count() > 0) {
                        foreach ($attributes as $ak => $av) {
                            $attributesFinal[$vv->source_id][$av->dimension] = $av->value;
                            $attributesFinal['key'][] = $av->dimension;
                            $attributesFinal['key'] = array_unique($attributesFinal['key']);
                        }
                    } else {
                        $attributesFinal[$vv->source_id] = [];
                        $attributesFinal['key'] = [];
                        $attributesFinal['key'] = [];
                    }
                }
            }

            $variants = @$shopifyRec->variants ? $shopifyRec->variants : [];
            $images = @$shopifyRec->images ?  $shopifyRec->images : [];


            $source_prices = [];

            for ($v = 0; $v < count($shopifyRec->variants); $v++) {

                $variants[$v]->min_price = $shopifyRec->variants[$v]->price;
                $variants[$v]->max_price = $shopifyRec->variants[$v]->price;
                if(count($shopifyRec->variants)>$v){
                    $variants[$v]->max_price = $shopifyRec->variants[count($shopifyRec->variants)-1]->price;
                }

                foreach ($variantRecs as $vRec) {
                    if ($shopifyRec->variants[$v]->sku == $vRec->source_id) {
                        $source_prices[] = $vRec->source_price;
                        $variants[$v]->min_source_price = $variantRecs[0]->source_price;
                        $variants[$v]->max_source_price = $variantRecs[count($variantRecs)-1]->source_price;
                        break;
                    }
                }
            }



            return ['success' => true, 'errors' => $errors, 'variants' => $variants,'source_prices'=>$source_prices, 'images' => $images, 'attributes' => $attributesFinal];

        } catch (QueryException $e) {
            $errors[] = $e->getMessage();
            return ['success' => false, 'errors' => $errors];
        }
    }

    public function getReview(Request $request){
        try{
            $product_id = $request->id;
            $product = DB::table('products')->select('reviews')->where('id', $product_id)->first();
            if( $product ){
                return ['success' => true, 'data' => json_decode($product->reviews)];
            }else{
                return ['success' => false, 'errors' => 'Product not found'];
            }
        }catch( \Exception $e ){
            return ['success' => false, 'errors' => $e];
        }
    }

    public function changeShowReview(Request $request){
        try{
            $product_id = $request->id;
            $is_show_front = $request->is_show_front;

            $product = DB::table('products')->select('reviews')->where('id', $product_id)->first();
            if( $product ){
                $reviews = (array)json_decode($product->reviews);
                $reviews['is_show_front'] = $is_show_front;
                DB::table('products')
                    ->where('id', $product_id)
                    ->update(['reviews' => json_encode($reviews)]);

                return ['success' => true, 'data' => 'review chnages successfully.'];
            }else{
                return ['success' => false, 'errors' => 'Product not found'];
            }
        }catch( \Exception $e ){
            return ['success' => false, 'errors' => $e];
        }
    }

    /**
     * Get product variants
     *
     * @return \Illuminate\Http\Response
     */
    public function getVariants()
    {

    }

    /**
     * Update product variants
     *
     * @return \Illuminate\Http\Response
     */
    public function saveVariants(Request $request)
    {
        $shop = Auth::user();

        $errors = [];

        $productID = $request->post('product_id');

        if (!preg_match('~^\d+$~', $productID)) {
            return [ 'success' => false, 'errors' => ["Invalid product id: $productID"]];
        }

        $productRec = DB::table('products')->where('id', $productID)->first();

        if (!$productRec) {
            return [ 'success' => false, 'errors' => ["Product id $productID was not found in the database."]];
        }

        $variants = json_decode($request->post('variants'));


        DB::beginTransaction();

        try {
            foreach ($variants as $variant) {

                // Update database record

                if( count($variants) == 1 && $variant->sku == ''){
                    DB::table('variants')
                        ->where([
                            ['product_id', '=', $productID]
                        ])
                        ->update(array('shopify_price' => floatval($variant->price)));
                }else{
                    DB::table('variants')
                        ->where([
                            ['product_id', '=', $productID],
                            ['source_id', '=', $variant->sku]
                        ])
                        ->update(array('shopify_price' => floatval($variant->price)));
                }


                // Update Shopify variant
                $modVariant = [
                    'id' => $variant->id,
                    'price' => $variant->price
                ];

                $variantReq = $shop->api()->rest('PUT', '/admin/api/'. env('SHOPIFY_API_VERSION') .'/variants/' . $variant->id . '.json', ['variant' => $modVariant]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $errors[] = $e->getMessage();
            return ['success' => false, 'errors' => $errors];
        }

        DB::commit();

        return ['success' => true, 'errors' => $errors];
    }

    /**
     * Delete product
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteProduct(Request $request)
    {
        $shop = Auth::user();

        $errors = [];

        $productID = $request->post('product_id');

        if (!preg_match('~^\d+$~', $productID)) {
            return [ 'success' => false, 'errors' => ["Invalid product id: $productID"]];
        }

        $productRec = DB::table('products')->where('id', $productID)->first();

        if (!$productRec) {
            return [ 'success' => false, 'errors' => ["Product id $productID was not found in the database."]];
        }

        DB::beginTransaction();

        try {
            $variantRecs = DB::table('variants')->where('product_id', $productID)->get();

            // Delete attributes
            foreach ($variantRecs as $vRec) {
                DB::table('attributes')->where('variant_id', $vRec->id)->delete();
            }

            // Delete variants
            DB::table('variants')->where('product_id', $productID)->delete();

            // Delete images
            DB::table('images')->where('product_id', $productID)->delete();

            // Delete tags
            DB::table('tags')->where('product_id', $productID)->delete();

            // Delete product rec
            DB::table('products')->where('id', $productID)->delete();

            // Delete product from Shopify
            // TODO: Verify deletion, throw exception if unsuccessful
            if ($productRec->shopify_id) {
                $productReq = $shop->api()->rest('DELETE', '/admin/api/'. env('SHOPIFY_API_VERSION') .'/products/' . $productRec->shopify_id . '.json');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $errors[] = $e->getMessage();
            return ['success' => false, 'errors' => $errors];
        }

        DB::commit();

        return ['success' => true, 'errors' => $errors];
    }

    public function updateAutoSetting(Request $request){
        try{
             $req = $request->auto_update;

             logger("auto-update call");
             logger(json_encode($req));

             $product_id = $req['product_id'];
             unset($req['product_id']);
                DB::table('products')
                ->where('id', $product_id)
                ->update(['auto_update' => json_encode($req)]);

             if( (int)$req['frequency'] == 0 ){
                 \Artisan::call('autoupdate:products ' . $product_id);
             }
            return ['success' => true];
        }catch ( \Exception $e ){
            return ['success' => false, 'errors' => $e];
        }
    }

    public function linkClicked(){
        try{
            $shop = Auth::user();
            $shop->is_clicked = 1;
            $shop->save();
            return ['success' => true];
         }catch ( \Exception $e ){
            return ['success' => false, 'errors' => $e];
        }
    }
}
