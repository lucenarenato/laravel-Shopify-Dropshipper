<?php

namespace App\Traits;

use App\Http\Controllers\Subscriber\SubscriberController;
use App\Models\Counters;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Keepa\API\Request as KeepaAPIRequest;
use Keepa\API\ResponseStatus;
use Keepa\helper\CSVType;
use Keepa\helper\CSVTypeWrapper;
use Keepa\helper\ProductAnalyzer;
use Keepa\KeepaAPI;
use App\Http\Controllers\AppController;

/**
 * Trait AutoUpdate.
 */
trait AutoUpdate
{
    public function index($productID, $is_old_key){
        try{
            logger('========== START:: Auto Update (index) =========');
            $product = \DB::table('products')->where('id', $productID)->first();

             logger("==== productID :: ".$productID);


            if( $product ){
                $source = $product->source;

            logger("==== user_id :: ".$product->user_id);


                $this->$source($productID, $product->user_id, $is_old_key);
            }else{
                logger("Product Not Found for this id ".$productID);
            }
            logger('========== END:: Auto Update (index) =========');
        }catch( \Exception $e ){
            logger('========== ERROR:: Auto Update (index) =========');
          //  logger(json_encode($e));
        }
    }

    public function Amazon($productID, $user_id, $is_old_key){
        try{
            logger('========== START:: Auto Update (Amazon) =========');
            $appC = new AppController();
            $db_product = \DB::table('products')->where('id', $productID)->first();
            $source_url =  $db_product->source_url;

            $to_currency = $this->getShopCurrency($db_product->user_id);
            $from_currency = get_country_currency($db_product->locale);

            preg_match('/\/([a-zA-Z0-9]{10})/',$source_url,$parsed);

            $asin = strtoupper($parsed[1]);

            $apiKey = ($is_old_key) ? config('const.keepa_api_key') : config('const.autoupdate_keepa_api_key');

            logger("====================== KEY =================");
            logger($apiKey);

            $api = new KeepaAPI($apiKey);

            $r = KeepaAPIRequest::getProductRequest(constant("Keepa\objects\AmazonLocale::$db_product->locale"), 0, null, null, 0,
                true,
                [$asin]);


            $response = $api->sendRequestWithRetry($r);

            $includeVariants = true;

            $apiVariants = [];
            $variants = [];
            $price = 0.00;

            if($to_currency !== $from_currency) {
                $latestFromRate = LatestExchangeRates('USD', $from_currency);
                $latestToRate = LatestExchangeRates('USD', $to_currency);
            }


            $is_error = false;
            switch ($response->status) {
                case ResponseStatus::OK:


                    // Check that at least one non-empty product is returned
                    if (isset($response->products) && is_array($response->products) && count($response->products) > 0 &&
                        isset($response->products[0]->title) && $response->products[0]->title) {

                        $product = $response->products[0];

                        $currentAmazonPrice = ProductAnalyzer::getLast($product->csv[CSVType::AMAZON], CSVTypeWrapper::getCSVTypeFromIndex(CSVType::AMAZON));

                        if ($currentAmazonPrice == -1) {
                            $currentAmazonPrice = ProductAnalyzer::getLast($product->csv[CSVType::MARKET_NEW],
                                CSVTypeWrapper::getCSVTypeFromIndex(CSVType::MARKET_NEW));
                        }

                        if ($currentAmazonPrice != -1) {
                            $price = number_format($currentAmazonPrice / 100, 2, '.', '');
//                           $price =  $appC->calculateCurrency($from_currency, $to_currency, $price);

                            $price  = ($from_currency==$to_currency) ? round($price, 2) : round((( $price * $latestToRate ) / $latestFromRate), 2);


                        }

                        // Add variants [extra call to API]
                        if ($includeVariants && $product->variations) {
                            foreach ($product->variations as $variant) {
                                $apiVariants[] = $variant->asin;
                                $variantAttributes[$variant->asin] = $variant->attributes;
                            }
                        }
                    } else {
                        $is_error = true;
                        logger('Could not fetch product. Please double-check your entry or try a different URL.');
                    }

                    break;
                default:
                    if ($response->error && $response->error->message && $response->error->message) {
                        logger($response->error->message);
                    }
            }

            // TODO: Re-factor to use single API call function for main product & variants
            if (count($apiVariants)) {
                if( count($apiVariants) > 100 ){
                    $apiVariants = array_slice($apiVariants, 0, config('const.max_variants_num'));
                }
                $r = KeepaAPIRequest::getProductRequest(constant("Keepa\objects\AmazonLocale::$db_product->locale"), 0, null, null,0,true, $apiVariants);

                $response = $api->sendRequestWithRetry($r);


                switch ($response->status) {
                    case ResponseStatus::OK:

                        // Check that at least one non-empty product is returned
                        if (isset($response->products) && is_array($response->products) && count($response->products) > 0 &&
                            isset($response->products[0]->title) && $response->products[0]->title) {
                            for ($i = 0; $i < min(config('const.max_variants_num'), count($response->products)); $i++) {
                                $product = $response->products[$i];

                                $currentAmazonPrice = ProductAnalyzer::getLast($product->csv[CSVType::AMAZON],
                                    CSVTypeWrapper::getCSVTypeFromIndex(CSVType::AMAZON));
                                if ($currentAmazonPrice == -1) {
                                    $currentAmazonPrice = ProductAnalyzer::getLast($product->csv[CSVType::MARKET_NEW],
                                        CSVTypeWrapper::getCSVTypeFromIndex(CSVType::MARKET_NEW));
                                }

                                $price = number_format($currentAmazonPrice / 100,
                                    2, '.', '');

//                               $variants[$i]['source_price'] = $appC->calculateCurrency($from_currency, $to_currency, $price);

                                $variants[$i]['source_price']  = ($from_currency==$to_currency) ? round($price, 2) : round((( $price * $latestToRate ) / $latestFromRate), 2);


                                $variants[$i]['source_id'] = $product->asin;
                            }
                        }

                    default:
                        if ($response->error && $response->error->message && $response->error->message) {
                            $is_error = true;
                            $errors[] = $response->error->message;
                        }
                }
            }


            if( !$is_error ){
                // update price
                $this->updatePrice($productID, $variants, $user_id, $price, 'amazon', $db_product->auto_update);

                // update counter
                $this->updateCounter($db_product->affiliate, $user_id, count($variants));
            }

            logger('========== END:: Auto Update (Amazon) =========');
        }catch( \Exception $e ){
            logger('========== ERROR:: Auto Update (Amazon) =========');
            logger($e);
        }
    }

    public function Walmart($productID, $user_id, $is_old_key){
        try{
            logger('========== START:: Auto Update (Walmart) =========');
            $is_error = false;
            $appC = new AppController();
            $db_product = \DB::table('products')->where('id', $productID)->first();

            $to_currency = $this->getShopCurrency($db_product->user_id);
            $from_currency = get_country_currency($db_product->locale);
            if($to_currency !== $from_currency) {
                $latestFromRate = LatestExchangeRates('USD', $from_currency);
                $latestToRate = LatestExchangeRates('USD', $to_currency);
            }

            $source_url =  $db_product->source_url;

            preg_match('/\/([0-9]{5,12})/',$source_url,$parsed);

            $asin = strtoupper($parsed[1]);

            $apiURL = config('const.walmart_api_url').$asin.'?apiKey='.config('const.walmart_api_key');

            $variants = [];
            $price = 0.00;
            try {
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($curl, CURLOPT_URL, $apiURL);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_USERAGENT,
                    'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.108 Safari/537.36');
                $data = curl_exec($curl);

                curl_close($curl);
                $data = json_decode($data, true);

                $price = (isset($data['salePrice'])) ? floatval($data['salePrice']) : 0.00;
                $price += (isset($data['standardShipRate'])) ? floatval($data['standardShipRate']) : $price;

                $price = number_format($price, 2, '.', '');
//                $price = ( isset($price) && $price != 0.00 ) ? $appC->calculateCurrency($from_currency, $to_currency, $price) : 0.00;

                $exchangePrice  = ($from_currency==$to_currency) ? round($price, 2) : round((( $price * $latestToRate ) / $latestFromRate), 2);

                $price = ( isset($price) && $price != 0.00 ) ? $exchangePrice : 0.00;




                if (isset($data['variants'])) {
                    for ($i = 0; $i < min(config('const.max_variants_num'), count($data['variants'])); $i++) {

                        $productInfo['variants'][$i] = [
                            'checked' => true,
                            'source_id' => $data['variants'][$i],
                            'title' => '',
                            'shipping_type' => 'Standard shipping',
                            'in_stock' => false,
                            'source_price' => 0.00,
                            'shopify_price' => 0.00,
                            'shipping_price' => 0.00,
                            'images' => [],
                            'main_image' => 0,
                            'inventory' => 10,
                            'attributes' => []
                        ];

                        $apiURL = config('const.walmart_api_url').$data['variants'][$i].'?apiKey='.config('const.walmart_api_key');

                        try {
                            $curl = curl_init();
                            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
                            curl_setopt($curl, CURLOPT_HEADER, false);
                            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                            curl_setopt($curl, CURLOPT_URL, $apiURL);
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($curl, CURLOPT_USERAGENT,
                                'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.108 Safari/537.36');
                            $variantData = curl_exec($curl);

                            if (curl_errno($curl)) {
                                throw new Exception(curl_error($curl));
                            }
                            curl_close($curl);
                            $variantData = json_decode($variantData, true);

                            if (isset($variantData['errors'])) {
                                logger('Could not find variant in Walmart database.');
                            }
                        } catch (Exception $e) {
                            $errors[] = $e->getMessage();
                        }

                        $price = (isset($data['salePrice'])) ? floatval($data['salePrice']) : 0.00;
                        $price += (isset($data['standardShipRate'])) ? floatval($data['standardShipRate']) : $price;

                        $price = number_format($price, 2, '.', '');

//                        $variants[$i]['source_price'] = ($price != 0.00) ? $appC->calculateCurrency($from_currency, $to_currency, $price) : $price;

                         $exchangePrice  = ($from_currency==$to_currency) ? round($price, 2) : round((( $price * $latestToRate ) / $latestFromRate), 2);


                        $variants[$i]['source_price'] = ($price != 0.00) ? $exchangePrice : $price;

                        logger('================='. $i .'================');
                        logger('================='. $variants[$i]['source_price'] .'================');

                        $variants[$i]['source_id'] = (@$variantData['itemId']) ? $variantData['itemId'] : '';
                    }
                }

            } catch (Exception $e) {
                $is_error = true;
                logger($e->getMessage());
            }

            if( !$is_error ){
                // update price
                $this->updatePrice($productID, $variants, $user_id, $price, 'walmart', $db_product->auto_update);

                // update counter
                $this->updateCounter($db_product->affiliate, $user_id, count($variants));
            }
            logger('========== END:: Auto Update (Walmart) =========');
        }catch( \Exception $e ){
            logger('========== ERROR:: Auto Update (Walmart) =========');
            logger($e);
        }
    }

    public function getShopCurrency($user_id){

 logger('========== START:: getShopCurrency =========');

        $shop = User::find($user_id);
        $parameter['fields'] = 'currency';
        $shop_result = $shop->api()->rest('GET', 'admin/api/'. env('SHOPIFY_API_VERSION') .'/shop.json', $parameter);


        if(!$shop_result->errors){
            $get_asset_call_limit = collect($shop_result->response->getHeader('x-shopify-shop-api-call-limit'))->first();

            logger("get_asset_call_limit ".$get_asset_call_limit);


                  if ($get_asset_call_limit == '39/40') {

                                    logger("Delay the script execution for 1 seconds ");
                                    logger("Get assets, Waiting 1 seconds for a credit");
                                    sleep(1);

                                }

        }
        return ( !$shop_result->errors ) ?
            $shop_result->body->shop->currency :
            '';
    }

    public function updatePrice($productID, $variants, $user_id, $price, $source, $autoUpdate){
        logger('====================== START:: updatePrice ======================');

        $shop = User::where('id', $user_id)->first();

        $autoUpdate = json_decode($autoUpdate);
        $saveShopify = (@$autoUpdate->save_shopify == 1 ) ? true : false;

        if( !empty($variants) ){
            foreach ( $variants as $vkey=>$vval ){
                logger('new_source_price :: ' . $vval['source_price']);
                $db_variant = DB::table('variants')->where('product_id', $productID)->where('source_id', $vval['source_id'])->first();
                if( $vval['source_price'] > 0 ){
                    if( $db_variant ){
                        logger('Variant_id :: ' . $db_variant->id);
                        $newPrice = $this->calculatePrice($db_variant->source_price, $db_variant->shopify_price, $vval['source_price']);

                        $modVariant = [
                            'variant' => [
                                'id' => $db_variant->shopify_id,
                                'price' => $newPrice,
                                'inventory_management' => null
                            ]
                        ];

                        DB::table('variants')->where([
                            ['product_id', '=', $productID],
                            ['source_id', '=', $vval['source_id']]
                        ])->update(['source_price' => $vval['source_price'], 'shopify_price' => $newPrice, 'updated_at' => date('Y-m-d H:i:s')]);

                        $variantReq = $shop->api()->rest('PUT', '/admin/api/'. env('SHOPIFY_API_VERSION') .'/variants/'.$db_variant->shopify_id.'.json', $modVariant);

                                if(!$variantReq->errors){

                            $get_asset_call_limit = collect($variantReq->response->getHeader('x-shopify-shop-api-call-limit'))->first();

                                logger("====variantReq::get_asset_call_limit");
                                logger($get_asset_call_limit);

                                      if ($get_asset_call_limit == '39/40') {

                                                        logger("Delay the script execution for 1 seconds ");
                                                        logger("Get assets, Waiting 1 seconds for a credit");
                                                        sleep(1);

                                                    }
                                                }

                    }
                }else{
                    ( $db_variant ) ? $this->setVariantOutOfStock($user_id, $db_variant->shopify_id) : '';
                }
            }
        }else{
            $db_variant = DB::table('variants')->where('product_id', $productID)->first();
            if( $price > 0 ){
                if( $db_variant ){
                    $newPrice = $this->calculatePrice($db_variant->source_price, $db_variant->shopify_price, $price);
                    $modVariant = [
                        'variant' => [
                            'id' => $db_variant->shopify_id,
                            'price' => $newPrice,
                            'inventory_management' => null
                        ]
                    ];

                    DB::table('variants')->where('product_id', $productID)->update([
                        'source_price' => $price, 'shopify_price' => $newPrice, 'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    $variantReq = $shop->api()->rest('PUT', '/admin/api/'. env('SHOPIFY_API_VERSION') .'/variants/'.$db_variant->shopify_id.'.json',
                        $modVariant);
 if(!$variantReq->errors){

$get_asset_call_limit = collect($variantReq->response->getHeader('x-shopify-shop-api-call-limit'))->first();

    logger("====variantReq else::get_asset_call_limit");
    logger($get_asset_call_limit);

          if ($get_asset_call_limit == '39/40') {

                            logger("Delay the script execution for 1 seconds ");
                            logger("Get assets, Waiting 1 seconds for a credit");
                            sleep(1);

                        }
                    }

                }
            }else{
                ( $db_variant ) ? $this->setVariantOutOfStock($user_id, $db_variant->shopify_id) : '';
            }
        }

        DB::table('products')
            ->where('id', $productID)
            ->update(['updated_at' => date('Y-m-d H:i:s')]);

        logger('====================== END:: updatePrice ======================');
    }
    public function updateCounter($affiliate, $user_id, $variantCount){
        logger('====================== START:: updateCounter ======================');

        logger('===== Auto update Counter :: Shop ID :: ' .$user_id .' ========');

        $charge = DB::table('charges')->where('user_id', $user_id)->where('status',"ACTIVE")->orderBy('created_at', 'desc')->first();
        $counter = Counters::where('user_id', $user_id)->where('charge_id', $charge->id)->where('status', 'active')->first();

        $affiliate = json_decode($affiliate);
        if ($affiliate->status) {
            $counter->auto_update_affiliate = $counter->auto_update_affiliate + 1;
        }else{
            $counter->auto_update_count = $counter->auto_update_count + 1;
        }

        $counter->save();


        logger('====================== END:: updateCounter ======================');
    }

    public function calculatePrice($old_source_price, $old_shopify_price, $new_source_price){

        //old method
        // $difference = $old_shopify_price - $old_source_price;
        // $new_shopify_price = ( $difference != 0 ) ? ($new_source_price + $difference) : $new_source_price;

        // new method



       if($old_shopify_price != $old_source_price  && $old_source_price > 0){

        $percentage = number_format((($old_shopify_price - $old_source_price) / $old_source_price * 100), 2);

        $percentage = str_replace(',', '', $percentage);

        logger("percentage ".$percentage);
        logger("new_source_price ".$new_source_price);
       

        $added_value = ( $new_source_price / 100 ) * $percentage;
 logger($added_value);
        $new_shopify_price = number_format( ( $new_source_price +  $added_value),  2);

        $new_shopify_price = str_replace(',', '', $new_shopify_price);

        $new_shopify_price = ( $new_shopify_price >= $new_source_price ) ? $new_shopify_price : $new_source_price;

      }
      else{
      	$new_shopify_price = $new_source_price;
      }


        return $new_shopify_price;
    }

    public function setVariantOutOfStock($user_id, $variantId){
        try{
            logger('====================== START:: setVariantOutOfStock ======================');
            $user = User::find($user_id);
            $inventory_id = $this->getVariantInventoryItemId($user_id, $variantId);

            if( $inventory_id != '' ){
                $inventoryLevels = $this->getVariantInventoryLevels($user_id, $inventory_id);
                if( !empty($inventoryLevels) ){
                    foreach ( $inventoryLevels as $ikey=>$ival ){
                        $this->setInventoryManagement($user_id, $variantId, 'shopify');
                        $endPoint = 'admin/api/'. env('SHOPIFY_API_VERSION') .'/inventory_levels/set.json';

                        $set = [
                            'location_id' => $ival->location_id,
                            'inventory_item_id' => $ival->inventory_item_id,
                            'available' => 0,
                        ];
                        $sh_set = $user->api()->rest('POST', $endPoint, $set);

                    }

                }
            }else{
                logger('====================== Inventory id not found ======================');
            }

            logger('====================== END:: setVariantOutOfStock ======================');
        }catch( \Exception $e ){
            logger('====================== ERROR:: setVariantOutOfStock ======================');
            logger($e);
        }
    }

    public function setInventoryManagement($user_id, $variantId, $type){
        try{
            logger('====================== START:: setInventoryManagement ======================');
            $user = User::find($user_id);
            $modVariant = [
                'variant' => [
                    'id' => $variantId,
                    'inventory_management' => $type
                ]
            ];
            $variantReq = $user->api()->rest('PUT', '/admin/api/'. env('SHOPIFY_API_VERSION') .'/variants/'.$variantId.'.json',
                $modVariant);

 if(!$variantReq->errors){
$get_asset_call_limit = collect($variantReq->response->getHeader('x-shopify-shop-api-call-limit'))->first();

    logger("====variantReq::get_asset_call_limit");
    logger($get_asset_call_limit);

          if ($get_asset_call_limit == '39/40') {

                            logger("Delay the script execution for 1 seconds ");
                            logger("Get assets, Waiting 1 seconds for a credit");
                            sleep(1);

                        }
                    }



            logger('====================== END:: setInventoryManagement ======================');
        }catch( \Exception $e ){
            logger('====================== ERROR:: setInventoryManagement ======================');
            logger($e);
        }
    }

    public function getVariantInventoryItemId($user_id, $variantId){
        try{
            logger('====================== START:: getVariantInventoryItemId ======================');
            $user = User::find($user_id);
            $inventory_id = '';
            if( $user ){
                $endPoint = 'admin/api/'. env('SHOPIFY_API_VERSION') .'/variants/' . $variantId . '.json';
                $parameter['fields'] = 'id,inventory_item_id';
                $sh_variant = $user->api()->rest('GET', $endPoint, $parameter);
                if( !$sh_variant->errors ){;
                    $inventory_id = (@$sh_variant->body->variant->inventory_item_id) ? $sh_variant->body->variant->inventory_item_id : '';
                }else{
                   // logger(json_encode($sh_variant));
                }
            }else {
                logger('====================== INTERNAL ERROR:: User '. $user_id .' not found... ======================');
            }

            logger('====================== END:: getVariantInventoryItemId ======================');
            return $inventory_id;
        }catch( \Exception $e ){
            logger('====================== ERROR:: getVariantInventoryItemId ======================');
            logger($e);
        }
    }

    public function getVariantInventoryLevels($user_id, $inventory_id){
        try{
            logger('====================== START:: getVariantInventoryLevels ======================');
            $user = User::find($user_id);
            if( $user ){
                $endPoint = 'admin/api/'. env('SHOPIFY_API_VERSION') .'/inventory_levels.json';
                $parameter['inventory_item_ids'] = $inventory_id;
                $sh_inventory_level = $user->api()->rest('GET', $endPoint, $parameter);
                if( !$sh_inventory_level->errors ){
                    return $sh_inventory_level->body->inventory_levels;
                }else{
                    logger(json_encode($sh_inventory_level));
                }
            }else {
                logger('====================== INTERNAL ERROR:: User '. $user_id .' not found... ======================');
            }

            logger('====================== END:: getVariantInventoryLevels ======================');
            return [];
        }catch( \Exception $e ){
            logger('====================== ERROR:: getVariantInventoryLevels ======================');
            logger($e);
        }
    }
}
