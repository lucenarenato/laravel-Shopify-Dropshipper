<?php

namespace App\Http\Controllers;

use App\Jobs\AddProductImagesJob;
use App\Jobs\ImportProductReviewJob;
use App\Jobs\ImportWalmartProductReviewJob;
use App\Models\BestsellerCategory;
use App\Models\Counters;
use App\Traits\ImportReview;
use Keepa\API\Request as KeepaAPIRequest;
use Keepa\API\ResponseStatus;
use Keepa\helper\CSVType;
use Keepa\helper\CSVTypeWrapper;
use Keepa\helper\KeepaTime;
use Keepa\helper\ProductAnalyzer;
use Keepa\helper\ProductType;
use Keepa\KeepaAPI;
use Keepa\objects\AmazonLocale;
use Osiset\ShopifyApp\Storage\Models\Charge;
use Osiset\ShopifyApp\Storage\Models\Plan;
use Symfony\Component\Intl\Currencies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProductReportSuggestion;
use App\Http\Requests\ProductReportSuggestionRequest;
use Carbon\Carbon;
use DateTime;
use App\Models\ShopifyShop;
use App\Events\WalmartProductFetchRealTimeMessage;
use App\Traits\KeepaCustomAPITrait;
class AppController extends Controller
{
    use ImportReview;
    use KeepaCustomAPITrait;

    /**
     * Show Import Products page
     *
     * @return \Illuminate\Http\Response
     */


    public function __construct()
    {

    }

    public function import()
    {
        $store = Auth::user()->name;
        return view('import', compact('store'));
    }

    /**
     * Get Amazon product info
     *
     * @return \Illuminate\Http\Response
     */
    public function getAmazonProduct(Request $request)
    {

        try {

            $amazon_domain = $request['hostname'];

            $to_currency = $this->getShopCurrency();

            $from_locale = $request['from_locale'];

            $from_currency_code = get_country_currency(strtoupper($from_locale));

            $from_currency = ( strtolower($from_locale) === 'uk' ) ? 'GBP' : $from_currency_code;

            $from_currency_symbol = Currencies::getSymbol($from_currency);


            $errors = [];
            $diagnostics = [];
            // Initialize product info array
            $productInfo = [
                'collection' => 0,
                'type' => '',
                'title' => '',
                'description' => '',
                'prime_eligible' => false,
                'in_stock' => false,
                'shipping_type' => 'Standard shipping',
                'source' => 'Amazon',
                'source_url' => '',
                'source_id' => '',
                'locale' => '',
                'source_price' => 0.00,
                'shopify_price' => 0.00,
                'shipping_price' => 0.00,
                'images' => [],
                'main_image' => 0,
                'inventory' => 10,
                'variants' => [],
                'attributes' => [],
                'tags' => '',
                'currency' => '',
                'currency_symbol' => Currencies::getSymbol($to_currency),
                'from_currency_symbol' => $from_currency_symbol,
                'review_count' => '',
                'saller_ranks' => [],
                'status' => 'active'
            ];

            // Initialize variant info arrays
            $apiVariants = [];
            $variantAttributes = [];

            // Check locale
            $locale = strtoupper($request->post('locale'));

            if (!preg_match('~^[A-Z]{2}$~',
                    $locale) || ($locale != 'BR' && !defined("Keepa\objects\AmazonLocale::".$locale))) {
                $errors[] = 'Invalid Amazon locale.';
            }

            $productInfo['locale'] = $locale;

            // Check Product ID (ASIN)
            $id = strtoupper($request->post('id'));

            if (!preg_match('~^[a-zA-Z0-9]{10}$~', $id)) {
                $errors[] = 'Invalid Amazon product identifier (ASIN).';
            }

            if ($errors) {
                return ['success' => false, 'errors' => $errors, 'product' => $productInfo];
            }

            $productInfo['source_id'] = $id;

            $includeVariants = $request->post('include_variants') ? true : false;

            /* Fetch product info from API
             *
             *  $domainID int Amazon locale of the product: AmazonLocale::US = 1     AmazonLocale::GB = 2    AmazonLocale::DE = 3
             *                                              AmazonLocale::FR = 4     AmazonLocale::JP = 5    AmazonLocale::CA = 6
             *                                              AmazonLocale::CN = 7     AmazonLocale::IT = 8    AmazonLocale::ES = 9
             *                                              AmazonLocale::IN = 10    AmazonLocale::MX = 11   AmazonLocale::AU = 12
             *                                              AmazonLocale::BR = 13 *BR is not officially documented yet but appears to be working
             *
             *  $offers int If specified (= not null) Determines the number of marketplace offers to retrieve. <b>Not available for Amazon China.</b>
             *
             *  $statsStartDate string Must ISO8601 coded date (with or without time in UTC). Example: 2015-12-31 or 2015-12-31T14:51Z.
             *                  If specified (= not null) the product object will have a stats field with quick access to current prices,
             *                  min/max prices and the weighted mean values in the interval specified statsStartDate to statsEndDate.
             *
             *  $statsEndDate string the end of the stats interval. See statsStartDate.
             *
             *  $update int If the product's last refresh is older than <i>update</i>-hours force a refresh.
             *              Use this to speed up requests if up-to-date data is not required. Might cost an extra token if 0 (= live data). Default 1.
             *
             *  $history bool Whether or not to include the product's history data (csv field). If you do not evaluate the csv field set to false to speed up the request and reduce traffic.
             *
             *  $asins string[] ASINs to request, must contain between 1 and 100 ASINs - or max 20 ASINs if the offers parameter is used.
             */
            $api = new KeepaAPI(config('const.keepa_api_key'));

//            $r = KeepaAPIRequest::getProductRequest(constant("Keepa\objects\AmazonLocale::$locale"), 0, null, null, 0,
//                true,
//                [$id]);


            $start_date = null;
            $end_date = null;

            $domainID = constant("Keepa\objects\AmazonLocale::$locale");
            $offers = 0;
            $history = true;


            $date = new Carbon('first day of january this year');
            $now = Carbon::now();

            $days = $date->diffInDays($now) -1;

            $productInfo['mainDomainId'] = $domainID;
            $productInfo['asin'] = $id;

           // $days = 180;
            $r =   KeepaAPIRequest::getProductRequest($domainID, $offers, $start_date, $end_date, 0, $history, [], [ 'asin' => $id,'priceTypes'=>0,"csv"=> 0]);


            $response = $api->sendRequestWithRetry($r);


            if($to_currency !== $from_currency) {

                $latestFromRate = LatestExchangeRates('USD', $from_currency);

                $latestToRate = LatestExchangeRates('USD', $to_currency);

            }

            $history = [];
            $history_is_graph = false;

            switch ($response->status) {

                case ResponseStatus::OK:

                    // Check that at least one non-empty product is returned
                    if (isset($response->products) && is_array($response->products) && count($response->products) > 0 &&
                        isset($response->products[0]->title) && $response->products[0]->title) {

                        $product = $response->products[0];
//                        if ($product->productType == ProductType::STANDARD || $product->productType == ProductType::DOWNLOADABLE) {
                        $productInfo['title'] = $product->title;
                        $productInfo['description'] = $product->description;

                        if ($product->features) {
                            $features = '';

                            foreach ($product->features as $feature) {
                                $features .= "<li>$feature</li>\n";
                            }

                            $productInfo['description'] = "<ul>\n$features\n</ul>\n".$productInfo['description'];
                        }
                        $productInfo['prime_eligible'] = $product->isEligibleForSuperSaverShipping;

                        $productInfo['review_count'] = ProductAnalyzer::getLast($product->csv[CSVType::RATING],
                            CSVTypeWrapper::getCSVTypeFromIndex(CSVType::RATING));
   //-----------------------------------------------------------------------------------------------------------
   // Amazon Price History : start
  //-----------------------------------------------------------------------------------------------------------

                        $amazon_history_price = [];
                        $amazon_history_date = [];
                        $amazon_history = [];
                        $new_price = [];
                        $amazon_csv = $product->csv[CSVType::AMAZON];


                        if(isset($amazon_csv) && count($amazon_csv)>0) {

                            for ($c = 0; $c < count($amazon_csv); $c++){
                                  if($c%2==0){
                                      //Timestamp in csv
                                      $UnixTimeDate = amazonKeepaTimeMinutesToUnixTime($amazon_csv[$c]);
                                     // $UnixTimeDate = $amazon_csv[$c];
                                    //  $date = date('Y-m-d', $UnixTimeDate);
                                      $date = date('Y-m-d', $UnixTimeDate);

                                    array_push($amazon_history_date, $date);


                                  }else{
                                      //price in csv
                                      $amazon_csv_price = $amazon_csv[$c];


                                      if($amazon_csv_price==-1){
                                          $amazon_csv_price = 0;
                                      }else{
                                          $amazon_csv_price = number_format($amazon_csv_price / 100, 2, '.', '');
                                      }


                                      array_push($amazon_history_price,$amazon_csv_price);
                                  }



                            }

                            }else{
                                   $history_is_graph = false;
                              }


                        foreach($amazon_history_price as $key=>$val){

                            if ($val == '0') {
                                unset($amazon_history_price[$key]);
                                unset($amazon_history_date[$key]);
                            }

                        }


                        $amazon_history_date = array_values($amazon_history_date);
                        $amazon_history_price = array_values($amazon_history_price);


                        if(count($amazon_history_date)>0 && count($amazon_history_price)>0) {

                            $history_is_graph = true;

                        }else{

                             if(isset($amazon_csv[0])){
                                 $start_date = $amazon_csv[0];
                                 $UnixTimeDate = amazonKeepaTimeMinutesToUnixTime($start_date);
                                 $date_start = date('Y-m-d', $UnixTimeDate);
                                 $date_end = date('Y-m-d');
                                 $date_range = date_range($date,$date_end, "+1 day", "Y-m-d");
                                 $amazon_history_date = $date_range;
                                // array_push($amazon_history_date, $date_start);
                             }else{
                                 $history_is_graph = false;
                             }



                        }



 //-----------------------------------------------------------------------------------------------------------
 //Amazon Price History : end
 //-----------------------------------------------------------------------------------------------------------


                        //Amazon Category/SubCategory of products : start


                        $categoryes_tree = $product->categoryTree;
                        $productInfo['categoryes_tree'] = $categoryes_tree;

                        //Amazon Category/SubCategory of products : end

                        $currentAmazonPrice = ProductAnalyzer::getLast($product->csv[CSVType::AMAZON],
                            CSVTypeWrapper::getCSVTypeFromIndex(CSVType::AMAZON));

                        if ($currentAmazonPrice == -1) {
                            $currentAmazonPrice = ProductAnalyzer::getLast($product->csv[CSVType::MARKET_NEW],
                                CSVTypeWrapper::getCSVTypeFromIndex(CSVType::MARKET_NEW));

                              $history_is_graph = false;

                        }

                        if ($currentAmazonPrice != -1) {
                            $productInfo['in_stock'] = true;
                            $price = number_format($currentAmazonPrice / 100, 2, '.', '');

//                                $productInfo['source_price'] =  $this->calculateCurrency($from_currency, $to_currency, $price);

                            //Amazon Price History : start

                            $history_is_graph = true;

                            if(count($amazon_history_price)<=0){
                                 for($i=0;$i<count($amazon_history_date); $i++){
                                     $amazon_csv_price = $price;
                                     array_push($new_price,$amazon_csv_price);
                                 }
                            }



                            if(count($amazon_history_price)<=0){
                                $amazon_history_date = array_values($amazon_history_date);
                                $amazon_history_price = array_values($new_price);
                            }


                           $array_date_price = array_combine($amazon_history_date,$amazon_history_price);

                            $sum_monthly_graph = array();
                            $tatal_price = [];

                            $count = 1;
                            foreach ($array_date_price as $date => $value){

                                $key = date("M Y", strtotime($date));

                                if (!isset($sum_monthly_graph[$key])){
                                    $tatal_price[$key] = $count;
                                    $sum_monthly_graph[$key] = $value;

                                } else{
                                    $tatal_price[$key] =$tatal_price[$key]+1;

                                    $sum_monthly_graph[$key]+= $value;
                                }

                            }

                             $avg_array_graph = [];

                            if(count($sum_monthly_graph)>0 && count($tatal_price)>0){

                                foreach ($tatal_price as $date => $count){


                                        $average_price = $sum_monthly_graph[$date] / $count; //$average = array_sum($array) / count($array);
                                        $average = round($average_price, 2);

                                        array_push($avg_array_graph,$average);

                                }
                            }



                            $amazon_history_price = $avg_array_graph;
                            $amazon_history_date = array_keys($sum_monthly_graph);


                            $amazon_history["price"] = $amazon_history_price;
                            $amazon_history["date"] = $amazon_history_date;

                            $history["amazon_price_history"] = $amazon_history;

                            //Amazon Price History : end



                            $productInfo['source_price'] = ($from_currency==$to_currency) ? round($price, 2) : round((( $price * $latestToRate ) / $latestFromRate), 2);



                            $productInfo['shopify_price'] = $productInfo['source_price'];

                        }
                        if ($product->imagesCSV) {
                            $imgArray = explode(',', $product->imagesCSV);
                            foreach ($imgArray as $img) {
                                $productInfo['images'][] = [
                                    'src' => config('const.amazon_images_url').$img, 'variant' => -1,
                                    'selected' => true
                                ];
                            }
                        }
                        // Add variants [extra call to API]
                        if ($includeVariants && $product->variations) {
                            foreach ($product->variations as $variant) {
                                $apiVariants[] = $variant->asin;
                                $variantAttributes[$variant->asin] = $variant->attributes;
                            }
                        }

                        // Set product attributes
                        if (isset($variantAttributes[$productInfo['source_id']])) {
                            $productInfo['attributes'] = $variantAttributes[$productInfo['source_id']];
                        }


                      //  dd($product);

                        // saller ranks
                       // $salesRank = ($product->salesRanks) ? $product->salesRanks : [];


//                        foreach ($salesRank as $key => $val) {
//                            if (end($val) != -1) {
//                            if(isset($product->categoryTree)){
//                                $categoryTree = $product->categoryTree;
//
//                                foreach ($categoryTree as $ckey => $cval) {
//
//                                   // $bestSallerRank =  $this->getBestSellerRank($amazon_domain,$cval->catId);
//
//                                    $productInfo['saller_ranks'][$key]['name'] = ($cval->catId == $key) ? $cval->name : '';
//                                }
//                            }
//                                $productInfo['saller_ranks'][$key]['count'] = end($val);
//
//                            }
//                        }
//                        }
                    } else {
                        $errors[] = 'Could not fetch product. Please double-check your entry or try a different URL.';
                    }

                    break;

                default:
                    if ($response->error && $response->error->message && $response->error->message) {
                        $errors[] = $response->error->message;
                    }
            }
            // Remove main variant from further API calls
//            if (($key = array_search($productInfo['source_id'], $apiVariants)) !== false) {
//                unset($apiVariants[$key]);
//            }

            // Make API call to retrieve product variants
            // TODO: Re-factor to use single API call function for main product & variants
            if (!count($errors) && count($apiVariants)) {
                if (count($apiVariants) > 100) {
                    $apiVariants = array_slice($apiVariants, 0, config('const.max_variants_num'));
                }
                $r = KeepaAPIRequest::getProductRequest(constant("Keepa\objects\AmazonLocale::$locale"), 0, null, null,
                    0, true, $apiVariants);

                $response = $api->sendRequestWithRetry($r);

                switch ($response->status) {
                    case ResponseStatus::OK:
                        // Check that at least one non-empty product is returned
                        if (isset($response->products) && is_array($response->products) && count($response->products) > 0 &&
                            isset($response->products[0]->title) && $response->products[0]->title) {
                            for ($i = 0; $i < min(config('const.max_variants_num'), count($response->products)); $i++) {

                                $productInfo['variants'][$i] = [
                                    'checked' => true,
                                    'title' => '',
                                    'in_stock' => false,
                                    'shipping_type' => 'Standard shipping',
                                    'source_id' => '',
                                    'source_price' => 0.00,
                                    'shopify_price' => 0.00,
                                    'shipping_price' => 0.00,
                                    'main_image' => 0,
                                    'inventory' => 10
                                ];

                                $product = $response->products[$i];

                                $productInfo['variants'][$i]['source_id'] = $product->asin;
                                $productInfo['variants'][$i]['title'] = $product->title;

                                $productInfo['variants'][$i]['prime_eligible'] = $product->isEligibleForSuperSaverShipping;

                                $currentAmazonPrice = ProductAnalyzer::getLast($product->csv[CSVType::AMAZON],
                                    CSVTypeWrapper::getCSVTypeFromIndex(CSVType::AMAZON));
                                if ($currentAmazonPrice == -1) {
                                    $currentAmazonPrice = ProductAnalyzer::getLast($product->csv[CSVType::MARKET_NEW],
                                        CSVTypeWrapper::getCSVTypeFromIndex(CSVType::MARKET_NEW));
                                }
                                if ($currentAmazonPrice != -1) {
                                    $productInfo['variants'][$i]['in_stock'] = true;
                                    $price = number_format($currentAmazonPrice / 100,
                                        2, '.', '');

//                                    $productInfo['variants'][$i]['source_price'] = $this->calculateCurrency($from_currency, $to_currency, $price);


                                    $source_price = ($from_currency == $to_currency) ? round($price,
                                        2) : round((($price * $latestToRate) / $latestFromRate), 2);


                                    $productInfo['variants'][$i]['source_price'] = $source_price;
                                    $productInfo['variants'][$i]['shopify_price'] = $productInfo['variants'][$i]['source_price'];
                                }
                                if ($product->imagesCSV) {

                                    $imgArray = explode(',', $product->imagesCSV);

                                    for ($j = 0; $j < count($imgArray); $j++) {
                                        $productInfo['images'][] = [
                                            'src' => config('const.amazon_images_url').$imgArray[$j], 'variant' => $i,
                                            'selected' => true
                                        ];
                                    }
//                                    $productInfo['variants'][$i]['main_image'] = count($productInfo['images']) - $j + 1;
                                    $mi = count($productInfo['images']) - $j + 1;
                                    $productInfo['variants'][$i]['main_image'] = ($mi <= count($productInfo['images']) - 1) ? $mi : count($productInfo['images']) - 1;
                                }
                                //                            if ($product->imagesCSV) {
                                //                                $imgArray = explode(',', $product->imagesCSV);
                                //                                $main_image = '';
                                //
                                //                                $f = 0;
                                //                                for ($j = 0; $j < count($imgArray); $j++) {
                                //                                    // START :: remove duplication of image
                                //                                    $srcs = [];
                                //                                    foreach ($productInfo['images'] as $key => $val) {
                                //                                        $srcs[] = $val['src'];
                                //                                    }
                                //                                    $img = config('const.amazon_images_url').$imgArray[$j];
                                //                                    if (in_array($img, $srcs)) {
                                //                                        $variant = array_search($img, $srcs);
                                //                                        if ($j == 0) {
                                //                                            $main_image = $variant;
                                //                                        }
                                //                                    } else {
                                //                                        $f++;
                                //                                        $variant = '';
                                //                                        $productInfo['images'][] = [
                                //                                            'src' => $img, 'variant' => $i,
                                //                                            'selected' => true
                                //                                        ];
                                //                                    }
                                //                                    // END :: remove duplication of image
                                ////                                    $productInfo['images'][] = ['src' => config('const.amazon_images_url') . $imgArray[$j], 'variant' => $i, 'selected' => true];
                                //                                }
                                //                                if ($main_image == '') {
                                //                                    if ($f > 0) {
                                //                                        $productInfo['variants'][$i]['main_image'] = count($productInfo['images']) - $f + 1;
                                //                                    }
                                //                                } else {
                                //                                    $productInfo['variants'][$i]['main_image'] = ($main_image) ? $main_image : $variant;
                                //                                }
                                ////                                $productInfo['variants'][$i]['main_image'] = count($productInfo['images']) - $j + 1;
                                //                            }

                                // Add variant attributes
                                if (isset($variantAttributes[$product->asin])) {
                                    $productInfo['variants'][$i]['attributes'] = $variantAttributes[$product->asin];
                                }
                            }
                        }

                    default:
                        if ($response->error && $response->error->message && $response->error->message) {
                            $errors[] = $response->error->message;
                        }
                }
            }

            // remove duplicate images
            foreach ($productInfo['images'] as $key => $val) {
                $srcs[] = $val['src'];
            }

            if (!empty($srcs)) {
                $u_images = array_unique($srcs);
                foreach ($u_images as $key => $val) {
                    $productInfo['unique_images'][] = ['src' => $val, 'selected' => true];
                }
            }

            // group by first attributes
            if (!empty($productInfo['variants'])) {
                $productInfo['variants'] = $this->group_by($productInfo['variants']);
            }

            // Diagnostics
            $diagnostics[] = ['name' => 'Request time (s)', 'value' => $response->requestTime / 1000];
            $diagnostics[] = ['name' => 'Processing time (s)', 'value' => $response->processingTimeInMs / 1000];
            $diagnostics[] = ['name' => 'Tokens consumed', 'value' => $response->tokensConsumed];
            $diagnostics[] = ['name' => 'Tokens left', 'value' => $response->tokensLeft];
            $diagnostics[] = ['name' => 'Tokens refill in (s)', 'value' => $response->refillIn / 1000];
            $diagnostics[] = ['name' => 'Tokens refill rate', 'value' => $response->refillRate];
            $diagnostics[] = ['name' => 'Tokens flow reduction', 'value' => $response->tokenFlowReduction];




            //START :: sales_estimation ========================================================================================



            $monthlySalesEstimateRes = $this->getAmazonRainForestData($id,$amazon_domain,"sales_estimation");

            //END :: sales_estimation ========================================================================================

            //START :: reviews ================================================================================================

            //$reviewsRes = $this->getAmazonRainForestData($id,$amazon_domain,"reviews");

            //END :: reviews ================================================================================================


            //START :: reviews ================================================================================================

            $productRes = $this->getAmazonRainForestData($id,$amazon_domain,"product");


            //END :: reviews ================================================================================================




           // dd($monthlySalesEstimateRes['monthly_sales_estimate']);

            //Amazon Price History : start

            $productInfo['history'] = $history;
            $productInfo['history_is_graph'] = $history_is_graph;

            $productInfo['monthly_sales_estimate'] = null;

            if(isset($monthlySalesEstimateRes['monthly_sales_estimate'])){
                $productInfo['monthly_sales_estimate'] = $monthlySalesEstimateRes['monthly_sales_estimate'];
            }

          if(isset($productRes['bestsellers_rank'])){
              $productInfo['saller_ranks'] = $productRes['bestsellers_rank'];
          }

            $productInfo['reviews_summary'] = [];

            if(isset($productRes['reviews_summary'])){
                $productInfo['reviews_summary'] = $productRes['reviews_summary'];
            }


            //Amazon Price History : end

            return ['errors' => $errors, 'diagnostics' => $diagnostics, 'product' => $productInfo];
        } catch (\Exception $e) {
            dd($e);
            $errors[] = $e->getMessage();
            return ['success' => false, 'errors' => $errors, 'product' => $productInfo];
//            return false;
        }
    }

    public function getAmazonRainForestData($id,$amazon_domain,$type){

        try {

        $response = [];

        $queryString = http_build_query([
            'api_key' => config('const.rainforest_api_key'),
            'type' => $type,
            'asin' => $id,
            'amazon_domain' => $amazon_domain
        ]);

        $result = $this->rainforestApiRequest($queryString);


            if (isset($result['request_info']) && $result['request_info']['success']) {

                if ($type == 'sales_estimation') {


                    $monthly_sales_estimate = 0;

                    if (isset($result['sales_estimation']) && $result['sales_estimation']) {

                        $sales_estimation = $result['sales_estimation'];
                        if (isset($sales_estimation['monthly_sales_estimate'])) {
                            $monthly_sales_estimate = $sales_estimation['monthly_sales_estimate'];
                        }
                    }

                    $response['monthly_sales_estimate'] = number_format($monthly_sales_estimate, 0);

                }else if ($type == 'reviews') {

                      $reviews_summary = [];
                        if(isset($result['summary'])) {

                            $reviews_summary = $result['summary'];

                        }

                        $response['reviews_summary'] = $reviews_summary;
               }else if ($type == 'product') {
                    $bestsellers_rank = [];

                    $reviews_summary = [];

                    if(isset($result['product'])) {

                        $product = $result['product'];

                        $reviews_summary['reviews_total'] = isset($product['reviews_total']) ? number_format($product['reviews_total'], 0) : 0;
                        $reviews_summary['rating'] = isset($product['rating']) ? $product['rating'] : 0;

                        if(isset($product['bestsellers_rank'])) {
                            $bestsellers_rank_data = $product['bestsellers_rank'];

                            if (count($bestsellers_rank_data) > 0) {
                                {
                                foreach ($bestsellers_rank_data as $key => $item) {
                                    $bestsellers_rank[$key]['count'] = $item['rank'];
                                    $bestsellers_rank[$key]['link'] = $item['link'];
                                    $bestsellers_rank[$key]['name'] = $item['category'];
                                }
                            }
                        }

                        }
                    }

                    $response['reviews_summary'] = $reviews_summary;
                    $response['bestsellers_rank'] = $bestsellers_rank;

                }



            }

        return $response;

        } catch (\Exception $e) {
            dd($e);
        }

    }



    public function getBestSellerRank($amazon_domain,$sales_estimation_category)
    {
        try {

            $errors = [];

            $queryString = http_build_query([
                'api_key' => config('const.rainforest_api_key'),
                'type' => 'bestsellers',
                'url' => "https://www.".$amazon_domain."/s/zgbs/pc/".$sales_estimation_category
            ]);

          //  https://api.rainforestapi.com/request?api_key=demo&type=bestsellers&url=https://www.amazon.com/s/zgbs/pc/516866

            $result = $this->rainforestApiRequest($queryString);

            dd($result);

            if ($result['request_info']['success']) {
                $result = array_slice($result['bestsellers'], 0, 10);
            } else {
                $result = [];
            }
            return ['success' => true, 'errors' => $errors, 'products' => $result];
        } catch (\Exception $e) {
            return ['success' => false, 'errors' => $e->getMessage()];
        }
    }


    public function getShopCurrency()
    {
        $shop = Auth::user();
        $parameter['fields'] = 'currency';

        $shop_result = $shop->api()->rest('GET', 'admin/api/'.env('SHOPIFY_API_VERSION').'/shop.json', $parameter);

        if (!$shop_result->errors) {
            return $shop_result->body->shop->currency;
        }
    }

    public function calculateCurrency($fromCurrency, $toCurrency, $amount)
    {
        try {
            $toCurrency = ($toCurrency == '') ? $fromCurrency : $toCurrency;
            $amount = urlencode($amount);
            $fromCurrency = urlencode($fromCurrency);
            $toCurrency = urlencode($toCurrency);
            $rawdata = file_get_contents("https://data.fixer.io/api/convert?access_key=3175c637bf1d91ef7dedd6640654bb1a&from=$fromCurrency&to=$toCurrency&amount=$amount&format=1");

            $data = json_decode($rawdata, true);
            return (@$data['result']) ? round($data['result'], 2) : 0.00;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function group_by($data)
    {

       // logger("=============START::group_by::attributes=============");
        try {

            foreach ($data as $key => $val) {
                if (!empty($val['attributes'])) {
                    $dimval[] = (is_array($val['attributes'][0])) ? $val['attributes'][0]['value'] : $val['attributes'][0]->value;
                }
            }

            $newDimVal = array_unique($dimval);

            //logger("newDimVal");
            //logger($newDimVal);

                foreach ($newDimVal as $key => $val) {


                    foreach ($data as $k => $v) {
                        if (isset($v['attributes'][0])) {
                            if (is_array($v['attributes'][0])) {
                                if ($v['attributes'][0]['value'] == $val) {
                                    $newProductVariants[] = $v;
                                }
                            } else {
                                if ($v['attributes'][0]->value == $val) {
                                    $newProductVariants[] = $v;
                                }
                            }
                        }
                    }
                }

            return $newProductVariants;
        } catch (\Exception $e) {
            dd($e);
        }

        logger("=============END::group_by::attributes=============");
    }

    /**
     * Get Walmart US product info
     *
     * @return \Illuminate\Http\Response
     */
    public function getWalmartProduct(Request $request)
    {

          $to_currency = $this->getShopCurrency();
        // $from_currency = $request['currency'];

            $from_locale = $request['from_locale'];

            $from_currency = get_country_currency(strtoupper($from_locale));

            $from_currency_symbol = Currencies::getSymbol($from_currency);


        $errors = [];
        $diagnostics = [];

        // Initialize product info array
        $productInfo = [
            'collection' => 0,
            'type' => '',
            'title' => '',
            'description' => '',
            'prime_eligible' => false,
            'in_stock' => false,
            'shipping_type' => 'Standard shipping',
            'source' => 'Walmart',
            'source_url' => '',
            'source_id' => '',
            'locale' => 'US',
            'source_price' => 0.00,
            'shopify_price' => 0.00,
            'shipping_price' => 0.00,
            'images' => [],
            'main_image' => 0,
            'inventory' => 10,
            'variants' => [],
            'attributes' => [],
            'tags' => '',
            'currency' => '',
            'currency_symbol' => Currencies::getSymbol($to_currency),
            'from_currency_symbol' => $from_currency_symbol,
            'history'=> [],
            'review_count' => '',
            'status' => 'active'
        ];

        // Check Walmart Product ID
        $productID = $request->post('product_id');

        if (!preg_match('~^[0-9]{5,12}$~', $productID)) {
            $errors[] = 'Invalid Walmart product id.';
        }

        if ($errors) {
            return ['success' => false, 'errors' => $errors, 'product' => $productInfo];
        }

        $apiURL = config('const.walmart_api_url').'?x-api-key='.config('const.walmart_api_key').'&product_id='.$productID;


        try {

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $apiURL,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 500,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
            ));
            $response = curl_exec($curl);

            if (curl_errno($curl)) {
                logger("curl_errno");
                throw new \Exception(curl_error($curl));
            }
            curl_close($curl);
            $data = json_decode($response, true);

            //logger("========================Walmart::API Response===============================");
            //logger($data);

            if (isset($data['message'])) {
                    $errors["api_rate_limit"] = $data['message'];
                    return ['success' => false, 'errors' => $errors, 'product' => $productInfo];
            }

            //START::Test Mode----

              // $data = jsonFileDataFetch();

            //END::Test Mode----

        } catch (\Exception $e) {
            logger("Error");
            $errors[] = $e->getMessage();
          //  logger(json_encode($errors));
        }

        if ($to_currency !== $from_currency) {
            $latestFromRate = LatestExchangeRates('USD', $from_currency);
            $latestToRate = LatestExchangeRates('USD', $to_currency);
        }

        $productInfo['source_id'] = $productID;

        $productInfo['title'] = isset($data['name']) ? $data['name'] : '';


        $product_information = " ";
        if(isset($data['product_information'])){
            $product_information = $data['product_information'];
            $product_information = array2ul($product_information);
        }

        $productInfo['description'] = isset($data['product_information']) ? $product_information : '';

        if (isset($data['detailed_description'])) {
            $productInfo['description'] .= "<br>\n".$data['detailed_description'];
        }

         if (isset($data['review_count'])) {
             $productInfo['review_count'] = $data['review_count'];
         }

        if (isset($data['product_reviews'])) {
            $productInfo['product_reviews'] = $data['product_reviews'];
        }

        if (isset($data['rating'])) {
            $productInfo['rating'] = $data['rating'];
        }

        if (isset($data['availability_status']) && $data['availability_status'] == 'In Stock.') {
            $productInfo['in_stock'] = true;
        }

        if (isset($data['sale_price'])) {
            $productInfo['source_price'] = floatval($data['sale_price']);
        }

        $price = number_format($productInfo['source_price'], 2, '.', '');

        $exchange_price = ($from_currency == $to_currency) ? round($price,
            2) : round((($price * $latestToRate) / $latestFromRate), 2);

        $productInfo['source_price'] = ($price != 0.00) ? $exchange_price : $price;
        $productInfo['shopify_price'] = $productInfo['source_price'];

        // Product images
        if (isset($data['images'])) {
            foreach ($data['images'] as $img) {
                $productInfo['images'][] = ['src' => $img, 'variant' => -1, 'selected' => true];
            }
        }

        // Variants
        $variantAttributes = [];

//  START:: WP::attributes=========================================================================================
        if (isset($data['attributes'])) {

            $attributes_list = $data['attributes'];
            $productInfo['attributes']  = $this->FilterAttributes($attributes_list);
        }


//  END:: WP::attributes============================================================================================


//  START:: WP::variation===========================================================================================
        if (isset($data['variation_item_id'])) {

            for ($i = 0; $i < min(config('const.walmart_max_variants_num'), count($data['variation_item_id'])); $i++) {

               // logger("variant ". $i);

                $productInfo['variants'][$i] = [
                    'checked' => true,
                    'source_id' => $data['variation_item_id'][$i],
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

                $variantApiURL = config('const.walmart_api_url').'?x-api-key='.config('const.walmart_api_key').'&product_id='.$data['variation_item_id'][$i];

                try {

                    set_time_limit(0);


                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $variantApiURL,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 500,
                        CURLOPT_FOLLOWLOCATION => false,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                    ));

                    if (curl_errno($curl)) {
                       // logger("curl_errno");
                        throw new \Exception(curl_error($curl));
                    }

                    $variantData = curl_exec($curl);
                    curl_close($curl);
                    $variantData = json_decode($variantData, true);

                   // logger("========================Walmart::Variant :: API Response===============================");
                   // logger($variantData);

                    if (isset($variantData['errors'])) {
                       // throw new \Exception('Could not find variant in Walmart database.');
                        $errors["api_rate_limit"] = "Operation timed out";
                        return ['success' => false, 'errors' => $errors, 'product' => $productInfo];
                    }

                    if (isset($variantData['message'])) {
                        $errors["api_rate_limit"] = $variantData['message'];
                        return ['success' => false, 'errors' => $errors, 'product' => $productInfo];
                    }

                } catch (\Exception $e) {
                    logger("variant  error". json_encode($e->getMessage()));
                    $errors[] = $e->getMessage();
                }

                //START::Test Mode----

               // $variantData = jsonFileDataFetch();

                //END::Test Mode----

                $productInfo['variants'][$i]['title'] = isset($variantData['name']) ? $variantData['name'] : '';

                if (isset($variantData['availability_status']) &&   $variantData['availability_status'] == 'In Stock.') {
                    $productInfo['variants'][$i]['in_stock'] = true;
                }

                if (isset($variantData['sale_price'])) {
                    $productInfo['variants'][$i]['source_price'] = floatval($variantData['sale_price']);
                }

                $price = number_format($productInfo['variants'][$i]['source_price'],
                    2, '.', '');

                $exchange_price = ($from_currency == $to_currency) ? round($price,
                    2) : round((($price * $latestToRate) / $latestFromRate), 2);

                $productInfo['variants'][$i]['source_price'] = ($price != 0.00) ? $exchange_price : $price;

                $productInfo['variants'][$i]['shopify_price'] = $productInfo['variants'][$i]['source_price'];

                // Product images
                if (isset($variantData['images'])) {

                    for ($j = 0; $j < count($variantData['images']); $j++) {
                        $productInfo['images'][] = [
                            'src' => $variantData['images'][$j], 'variant' => $i,
                            'selected' => true
                        ];
                    }

                    $productInfo['variants'][$i]['main_image'] = count($productInfo['images']) - $j + 1;
                }

                // Attributes
                $productInfo['variants'][$i]['attributes'] = [];

                if (isset($variantData['attributes'])) {
                    $variantAttributes_list = $variantData['attributes'];
                    $productInfo['variants'][$i]['attributes'] = $this->FilterAttributes($variantAttributes_list);

                }
            }
        }
//  END:: WP::variation===============================================================================================

        // remove duplicate images
       foreach ($productInfo['images'] as $key => $val) {
            $srcs[] = $val['src'];
        }
        if (!empty($srcs)) {
            $u_images = array_unique($srcs);
            foreach ($u_images as $key => $val) {
                $productInfo['unique_images'][] = ['src' => $val, 'selected' => true];
            }
        }
        // group by first attributes
        if (!empty($productInfo['variants'])) {
            $productInfo['variants'] = $this->group_by($productInfo['variants']);
           // $productInfo['variants'] = $productInfo['variants'];
        }

        //Walmart Category/SubCategory of products : start
        $categoryes_tree = $data['product_category'];
        $productInfo['categoryes_tree'] = $categoryes_tree;
        //Walmart Category/SubCategory of products : end

      //  logger("productInfo");
      //  logger($productInfo);

        return ['errors' => $errors, 'diagnostics' => $diagnostics, 'product' => $productInfo];
    }


//  START:: WP::attributes==========
    public function FilterAttributes($ProductAttributes){

        $skipAttrs = [
            'productUrlText', 'wklyFcstWeeks', 'isPvtLabelUnbranded', 'uniqueProductId', 'actualColor',
            'productUrlText',
            'finerCateg', 'compositeWood', 'isPvtLabelUnbranded', 'replenishmentStartDate', 'isImport', 'mainimageurl',
            'batteryType', 'isSortable', 'ppuQty', 'paperWoodIndicator', 'ppuQtyUom', 'prodClassType',
            'ironBankCategory',
            'canonicalUrl', 'gender', 'isOrderable', 'fuelRestriction', 'clothingSize',
            'caResidentsPropWarningRequired',
            'hasWarranty', 'mirrorNum', 'replenishmentEndDate', 'pesticideInd', 'assemblyRequiredInd', 'replenType',
            'requiresTextileActLabeling', 'karfIsAlcohol', 'globalProductType', 'globalProductTypeCharpath',
            'ConsumerItemNumber'
        ];

        $productAttributesInfo = [];

        if ($ProductAttributes) {

            $attributes_list = $ProductAttributes;

            $attributes_array = (explode(",",$attributes_list));

            if(count($attributes_array) > 0) {

                foreach ($attributes_array as $attributes_item) {

                    $attributes = (explode(":",$attributes_item));

                    if(count($attributes) > 0) {

                        $key = $attributes[0];
                        $value = $attributes[1];
                        if (!in_array($key, $skipAttrs)) {
                            $variantAttributes[] = $key;
                            // Set product attributes
                            $productAttributesInfo[] = ['dimension' => ucfirst($key), 'value' => ucfirst($value)];
                        }
                    }
                }
            }
        }

        return $productAttributesInfo;

    }
//  END:: WP::attributes==========


    /**
     * Get a list of collections for the current shop
     *
     * @return \Illuminate\Http\Response
     */



    public function getCollectionsCategory(){
        try {
            $shop = Auth::user();

            $collections = getShopCollectionsCategoryData($shop);

            return ['success' => true, 'collections' => $collections];

        } catch (\Exception $e) {
            dump($e);
        }
    }


    public function getTags(){
        try {
            $shop = Auth::user();

            $tags = DB::table('products')
                ->join('tags', 'products.id', '=', 'tags.product_id')
                ->where('products.user_id', $shop->id)
                ->orderBy('tags.id', 'desc')
                ->get();

            return ['success' => true, 'tags' => $tags];

        } catch (\Exception $e) {
            dump($e);
        }
    }

    public function getShopifyTags(){
        try {
            $shop = Auth::user();

            $tags_list = [];
            $tags = getShopTagsData($shop);

            if(count($tags)>0) {
                foreach ($tags as $tag) {

                    $tags_list[] = $tag->node;

                }
            }

            return ['success' => true, 'tags' => $tags_list];

        } catch (\Exception $e) {
            dump($e);
        }
    }


    public function getCollections(Request $request)
    {
        try {
            $shop = Auth::user();

            $errors = [];

            $collections = [];


            $user_id = $shop['id'];

            $shopInfo = ShopifyShop::where('user_id','=',$user_id)->first();

           // logger("shopInfo => ". $shopInfo);

            if(!$shopInfo){

                $shopApiReq = $shop->api()->rest('GET', '/admin/shop.json');

               // logger("==shopApi==");
               // logger(json_encode($shopApiReq));

                if (!$shopApiReq->errors) {

                    //logger("====================================");

                   // logger(json_encode($shopApiReq->body->shop));

                    $shopApi =  (array) $shopApiReq->body->shop;

                  //  logger($shopApi['id']);

                  //  logger("Shop's API objct:".json_encode($shopApi));

                    ShopifyShop::updateOrCreate(
                        ["shop_id" => $shopApi['id']],
                        [
                            "shop_id" => $shopApi['id'],
                            "name" => $shopApi['name'],
                            "email" => $shopApi['email'],
                            "domain" => $shopApi['domain'],
                            "province" => $shopApi['province'],
                            "country" => $shopApi['country'],
                            "address1" => $shopApi['address1'],
                            "zip" => $shopApi['zip'],
                            "city" => $shopApi['city'],
                            "primary_locale" => $shopApi['primary_locale'],
                            "country_code" => $shopApi['country_code'],
                            "country_name" => $shopApi['country_name'],
                            "currency" => $shopApi['currency'],
                            "shop_owner" => $shopApi['shop_owner'],
                            "weight_unit" => $shopApi['weight_unit'],
                            "province_code" => $shopApi['province_code'],
                            "plan_name" => $shopApi['plan_name'],
                            "user_id" => $user_id
                        ]
                    );

                   // logger("-----------------------------------");
                   // logger("Shop Data is Saved!");
                    //logger("-----------------------------------");
                } else {
                   // logger("error during call Shop API !");
                }

            }


            $shope_data = getShopData($user_id);

            if(!$shope_data){
              $shope_data = getShopDataFromAPI($shop, 'id, email, name');
            }


            $apiRequest = $shop->api()->rest('GET', '/admin/api/'. env('SHOPIFY_API_VERSION') .'/custom_collections.json', ['limit' => 250]);


            if (isset($apiRequest->body->custom_collections)) {
                $collections = $apiRequest->body->custom_collections;
            }

            //fetch associate details
            $res = DB::table('amazon_associates')->select('locale', 'associate_id')->where('user_id', $shop->id)->get();
            $associates = $res->map(function ($name) {
                return $name->locale;
            })->toArray();

            //fetch bestseller category

            $bestsellerCategory = BestsellerCategory::select('name', 'url')->get()->toArray();

            // get counters detailed, and add counter if new plan activated
            $charge = DB::table('charges')->where('status', 'ACTIVE')->where('user_id',
                $shop->id)->orderBy('created_at', 'desc')->first();

            $counter = Counters::where('user_id', $shop->id)->where('status', 'active')->where('plan_id',
                1)->where('charge_id', '')->first();

            if ($counter) {
                $counter['charge_id'] = $charge->id;
                $counter->save();
            }
            $counter = Counters::where('user_id', $shop->id)->where('status', 'active')->where('charge_id',
                $charge->id)->first();
            if (!$counter) {
                $last_plan = Counters::where('user_id', $shop->id)->where('status', 'active')->first();
                if ($last_plan) {
                    $last_plan_id = $last_plan->plan_id;
                    $last_plan->status = 'canceled';
                    $last_plan->save();
                } else {
                    $last_plan_id = 1;
                }

                $counter = new Counters;
                $counter->user_id = $shop->id;
                $counter->charge_id = $charge->id;
                $counter->plan_id = $shop->plan_id;

                if ($counter->plan_id == 1) {
                    $histryCounter = Counters::where('user_id', $shop->id)->where('plan_id', 1)->orderBy('created_at',
                        'desc')->first();
                    if ($histryCounter) {
                        $counter->regular_product_count = $histryCounter->regular_product_count;
                        $counter->regular_product_variant_count = $histryCounter->regular_product_variant_count;
                        $counter->affiliate_product_count = $histryCounter->affiliate_product_count;
                        $counter->affiliate_product_variant_count = $histryCounter->affiliate_product_variant_count;
                        $counter->auto_update_count = $histryCounter->auto_update_count;
                        $counter->auto_update_affiliate = $histryCounter->auto_update_affiliate;
                    } else {
                        $counter->regular_product_count = 0;
                        $counter->regular_product_variant_count = 0;
                        $counter->affiliate_product_count = 0;
                        $counter->affiliate_product_variant_count = 0;
                    }
                } else {
                    if ($last_plan_id == 1) {
                        $counter->regular_product_count = 0;
                        $counter->regular_product_variant_count = 0;
                        $counter->affiliate_product_count = 0;
                        $counter->affiliate_product_variant_count = 0;
                    } else {
                        if ($last_plan) {
                            if (date('Y-m-d H:i:s') > $last_plan->end_date) {
                                $counter->regular_product_count = 0;
                                $counter->regular_product_variant_count = 0;
                                $counter->affiliate_product_count = 0;
                                $counter->affiliate_product_variant_count = 0;
                            } else {
                                $counter->regular_product_count = $last_plan->regular_product_count;
                                $counter->regular_product_variant_count = $last_plan->regular_product_variant_count;
                                $counter->affiliate_product_count = $last_plan->affiliate_product_count;
                                $counter->affiliate_product_variant_count = $last_plan->affiliate_product_variant_count;
                                $counter->auto_update_count = $last_plan->auto_update_count;
                                $counter->auto_update_affiliate = $last_plan->auto_update_affiliate;
                            }
                        }
                    }
                }

                $counter->status = 'active';
                $counter->is_disable_freemium = ($shop->plan_id == 1) ? 0 : 1;
                $counter->start_date = date('Y-m-d H:i:s');
                $counter->end_date = date('Y-m-d H:i:s', strtotime($counter->start_date.' + 30 days'));
                $counter->save();
            }
            $plan_detail = DB::table('plans')->where('id', $shop->plan_id)->first();
            $curr_plan = $shop->plan_id;
            $plan['affiliate_pr_err_msg'] = '';
            $plan['total_products'] = $counter->regular_product_count;
            $plan['total_affiliate_products'] = $counter->affiliate_product_count;
            $plan['current_plan_name'] = $plan_detail->name;
            $plan['current_plan_id'] = $plan_detail->id;
            if ($plan_detail->max_affiliate_product_import != 'unlimited') {
                // $plan['affiliate_pr_err_msg'] = ($plan_detail->max_affiliate_product_import != 'unlimited' && ($counter->affiliate_product_count +  $counter->auto_update_affiliate) >= $plan_detail->max_affiliate_product_import) ? 'With the '. $plan_detail->name .' plan you can import maximum ' . $plan_detail->max_affiliate_product_import . ' affiliate products, for more import <a href="/settings">please upgrade your plan</a>.' : '';
                $plan['affiliate_pr_err_msg'] = ($plan_detail->max_affiliate_product_import != 'unlimited' && $counter->affiliate_product_count >= $plan_detail->max_affiliate_product_import) ? 'With the '.$plan_detail->name.' plan you can import maximum '.$plan_detail->max_affiliate_product_import.' affiliate products, for more import <a href="/settings">please upgrade your plan</a>.' : '';
            }
            // $plan['regular_pr_err_msg'] = ($counter->regular_product_count + $counter->auto_update_count) >= $plan_detail->max_regular_product_import ? 'With the '. $plan_detail->name .' plan you can import maximum ' . $plan_detail->max_regular_product_import . ' regular products, for more import <a href="/settings">please upgrade your plan</a>.' : '';
            $plan['regular_pr_err_msg'] = $counter->regular_product_count >= $plan_detail->max_regular_product_import ? 'With the '.$plan_detail->name.' plan you can import maximum '.$plan_detail->max_regular_product_import.' regular products, for more import <a href="/settings">please upgrade your plan</a>.' : '';
            if ($curr_plan == 5) {
                // $plan['regular_pr_err_msg'] = ($counter->regular_product_count + $counter->auto_update_count) >= $plan_detail->max_regular_product_import ? 'With the '. $plan_detail->name .' plan you can import maximum ' . $plan_detail->max_regular_product_import . ' regular products.' : '';
                $plan['regular_pr_err_msg'] = $counter->regular_product_count >= $plan_detail->max_regular_product_import ? 'With the '.$plan_detail->name.' plan you can import maximum '.$plan_detail->max_regular_product_import.' regular products.' : '';
            }

            return [
                'success' => true, 'errors' => $errors, 'collections' => $collections, 'associates' => $associates,
                'plan' => $plan, 'bestseller_category' => $bestsellerCategory, "shope_data" =>$shope_data
            ];
        } catch (\Exception $e) {
            dump($e);
        }
    }

    /**
     * Get a list of products
     *
     * @return \Illuminate\Http\Response
     */
    public function getProducts(Request $request)
    {
        $shop = Auth::user();

        $errors = [];

        try {
            $products = DB::table('products')->get();

        } catch (QueryException $e) {
            $errors[] = $e->getMessage();
            return ['success' => false, 'errors' => $errors];
        }


        return ['success' => true, 'errors' => $errors, 'products' => $products];
    }

    /**
     * Add new product
     *
     * @return \Illuminate\Http\Response
     */
    public function addProduct(Request $request)
    {
        $shop = Auth::user();

        $errors = [];

        // TODO: Check if product import limit has been reached (billing)

        // Get product information in JSON format
        $productInfo = json_decode($request->post('product_info'));

       // logger("====productInfo====");
       // logger(json_encode($productInfo));

        $review_row = json_decode($request->post('review'));
        $review_row->status = $review_row->is_import ? 'In progress' : 'No';


        //Keepa Tracking::START===================================================================================
        $is_tracked = 0;
        $updateInterval = 1;
//        if($productInfo->source=="Amazon") {
//
//            $asin = $productInfo->asin;
//            $domainID = $productInfo->mainDomainId;
//          //  $updateInterval = rand(1,24);
//            $updateInterval = 1;
//            $tracking_param = [
//                'asin' => $asin,
//                'mainDomainId' => $domainID,
//                'updateInterval' => $updateInterval
//            ];
//
//            $TrackRes = $this->keepaAddProductTracking($shop->id,$tracking_param);
//
//            if($TrackRes){
//                $is_tracked = 1;
//            }
//
//        }
        //Keepa Tracking::END=======================================================================================




        // ob_start();
        // var_dump($productInfo);
        // $result = ob_get_clean();

        DB::beginTransaction();
        try {
            $affiliate = json_decode($request->post('affiliate'));
            if ($affiliate->status) {
                $res = DB::table('amazon_associates')->select('locale', 'associate_id')->where('user_id',
                    $shop->id)->get();
                $new = $res->map(function ($name) {
                    return $name->locale;
                })->toArray();
                $country = ($affiliate->country == 'COM') ? 'US' : $affiliate->country;
                $country = ($affiliate->country == 'UK') ? 'GB' : $country;
                if (in_array($country, $new)) {
                    $res = DB::table('amazon_associates')->select('associate_id')->where('user_id',
                        $shop->id)->where('locale', $country)->first();
                    if (strpos($productInfo->source_url, '?') !== false) {
                        $productInfo->source_url .= '&tag='.$res->associate_id;
                    } else {
                        $productInfo->source_url .= '?tag='.$res->associate_id;
                    }
                }
            }
            // Initialize Shopify product
            $shopifyProduct = [
                'title' => $productInfo->title,
                'body_html' => $productInfo->description,
                'status' => $productInfo->status,
                'metafields' => [
                    [
                        'key' => 'amazonedropship',
                        'value' => 'byamazone',
                        'value_type' => 'string',
                        'namespace' => 'dropshipper'
                    ]
                ]
            ];

            if ($productInfo->type) {
                $shopifyProduct['product_type'] = $productInfo->type;
            }

            if ($productInfo->collection) {
                $shopifyProduct['collection'] = $productInfo->collection;
            }

            $history_row = $productInfo->history;
            $categoryes_tree = $productInfo->categoryes_tree;

            // Save product
            $productRec = [
                'user_id' => $shop->id,
                'collection_id' => $productInfo->collection,
                'type' => $productInfo->type,
                'locale' => $productInfo->locale,
                'source' => $productInfo->source,
                'source_url' => $productInfo->source_url,
                'description' => $productInfo->description,
                'affiliate' => $request->post('affiliate'),
                'saller_ranks' => (@$productInfo->saller_ranks) ? json_encode($productInfo->saller_ranks) : '',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
                'auto_update' => $request->post('autoupdate'),
                'reviews' => json_encode($review_row),
                'history' => json_encode($history_row),
                'categoryes_tree' => json_encode($categoryes_tree),
                'status' => $productInfo->status,
                'is_tracked' => $is_tracked,
                'updateInterval' => $updateInterval
            ];

//            if( !$review_row->is_import ){
//                $productRec['reviews'] = json_encode($review_row);
//            }

            $productID = DB::table('products')->insertGetId($productRec);

            if (!$productID) {
                throw new \Exception('Failed to create product record.');
            }

            // Save images
            $mainProductImageID = null;
            $imageIDArray = [];
            $imageSrcArray = [];
            $shopifyImages = [];
            $variantMainImageURLs = ['']; // Initialize to empty URL for index integrity
            if ($productInfo->unique_images) {
                for ($i = 0; $i < count($productInfo->unique_images); $i++) {

                    // TODO: Add logic to make sure if variant image and !selected then still save
                    if ((!$productInfo->unique_images[$i]->selected && $i != $productInfo->main_image)) {
                        $imageIDArray[] = 0;
                        break;
                    }

                   // logger("variant image ".$i);
                   // logger("variant image unique_images".$productInfo->unique_images[$i]->src);

                    $imageRec = [
                        'product_id' => $productID,
                        'url' => $productInfo->unique_images[$i]->src,
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s")
                    ];

                    $imageID = DB::table('images')->insertGetId($imageRec);

                    if (!$imageID) {
                        throw new \Exception('Failed to create image record for '.$productInfo->unique_images[$i]->src.'.');
                    }

                    $imageIDArray[] = $imageID;
                    $imageSrcArray[$productInfo->unique_images[$i]->src] = $imageID;
                    if ($i == $productInfo->main_image) {
                        $mainProductImageID = $imageID;
                        $shopifyImages[] = ['src' => $productInfo->unique_images[$i]->src, 'position' => 1];
                        $variantMainImageURLs[0] = $productInfo->unique_images[$i]->src;
                    } else {
                        $shopifyImages[] = ['src' => $productInfo->unique_images[$i]->src];
                    }

                   // logger("shopifyImages");
                   // logger($shopifyImages);

                }
            }




//            if ($productInfo->images) {
//
//
//                for ($i = 0; $i < count($productInfo->images); $i++) {
//
//                    // TODO: Add logic to make sure if variant image and !selected then still save
//                    if ((!$productInfo->images[$i]->selected && $i != $productInfo->main_image)) {
//                        $imageIDArray[] = 0;
//                        break;
//                    }
//
//                    $imageRec = [
//                        'product_id' => $productID,
//                        'url' => $productInfo->images[$i]->src,
//                        'created_at' => date("Y-m-d H:i:s"),
//                        'updated_at' => date("Y-m-d H:i:s")
//                    ];
//
//                    logger("imageRec ".$i);
//                    logger($imageRec);
//
//                    $imageID = DB::table('images')->insertGetId($imageRec);
//
//                    logger("imageID".$imageID);
//
//                    if (!$imageID) {
//                        throw new \Exception('Failed to create image record for '.$productInfo->images[$i]->src.'.');
//                    }
//
//                    $imageIDArray[] = $imageID;
//                    $imageSrcArray[$productInfo->images[$i]->src] = $imageID;
//
//                    logger("main_image");
//                    logger($productInfo->main_image);
//
//                    if ($i == $productInfo->main_image) {
//                        $mainProductImageID = $imageID;
//                        $shopifyImages[] = ['src' => $productInfo->images[$i]->src, 'position' => 1];
//                        $variantMainImageURLs[0] = $productInfo->images[$i]->src;
//                    } else {
//                        $shopifyImages[] = ['src' => $productInfo->images[$i]->src];
//                    }
//
//                    logger("imageIDArray");
//                    logger($imageIDArray);
//
//                }
//            }

            // Add images to Shopify product
            if ($shopifyImages) {
                // add maximum 35 image in shopify while create product
                $shopifyProduct['images'] = (count($shopifyImages) > 35) ? array_slice($shopifyImages, 0,
                    34) : $shopifyImages;
            }

            // Save tags
            $tags = trim($productInfo->tags);
            $shopifyTags = [];

            if ($tags != '') {

                $tagsArray = explode(',', $tags);

                foreach ($tagsArray as $tag) {

                    $tag = trim($tag);

                    $shopifyTags[] = $tag;

                    DB::table('tags')->insert(['product_id' => $productID, 'tag' => $tag]);
                }

                // Add tags to shopify product
                $shopifyProduct['tags'] = $shopifyTags;
            }
            // Construct variant records
            $variantRecs = [];
            $variantAttsRecs = [];
            $shopifyVariants = [];

            $mainproduct_shopify_price = floatval($productInfo->shopify_price);
            if(count($productInfo->variants)>0){
                if(isset($productInfo->variants[0]->shopify_price)){
                    if($productInfo->source_id==$productInfo->variants[0]->source_id){
                        $mainproduct_shopify_price = floatval($productInfo->variants[0]->shopify_price);
                    }
                }
            }


            // Add main variant
            $variantRecs[] = [
                'product_id' => $productID,
                'image_id' => $mainProductImageID,
                'is_main' => true,
                'title' => $productInfo->title,
                'in_stock' => $productInfo->in_stock,
                'inventory' => $productInfo->inventory,
                'prime_eligible' => $productInfo->prime_eligible,
                'shipping_type' => $productInfo->shipping_type,
                'source_id' => $productInfo->source_id,
                'source_price' => floatval($productInfo->source_price),
                'shipping_price' => floatval($productInfo->shipping_price),
                'shopify_price' => $mainproduct_shopify_price,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ];
            // Add main variant to Shopify product
            $mainShopifyVariant = [
                'title' => $productInfo->title,
                'sku' => $productInfo->source_id,
                'price' => $mainproduct_shopify_price,
                'inventory_quantity' => $productInfo->inventory,
                'inventory_management' => 'shopify',
                'inventory_policy' => 'deny'
            ];

            $attsArray = [];

            if ($productInfo->attributes) {

                // Set shopify options
                $shopifyOptions = [];

                $shopifyOptNum = 0;

                foreach ($productInfo->attributes as $attr) {
                    $attsArray[] = ['dimension' => $attr->dimension, 'value' => $attr->value];
                    if(count($shopifyOptions) <= 2) {
                        $shopifyOptions[] = ['name' => $attr->dimension];

                        $mainShopifyVariant['option'.++$shopifyOptNum] = $attr->value;
                    }
                }

                $shopifyProduct['options'] = $shopifyOptions;
            }

            $shopifyOptions = [];
            if (empty($productInfo->attributes)) {
                if (!empty($productInfo->variants[0])) {
                    $cus_options = $productInfo->variants[0]->attributes;

                        foreach ($cus_options as $ckey => $cval) {

                            $shopifyOptions[] = ['name' => $cval->dimension];

                          }


                    $shopifyProduct['options'] = $shopifyOptions;
                }
            }


            $shopifyVariants[] = $mainShopifyVariant;

            $variantAttsRecs[] = $attsArray;
            // Add other variants
            if ($productInfo->variants) {

              //  logger("total variants");
             //   logger(count($productInfo->variants));
              //  logger($productInfo->variants);

                for ($v = 0; $v < count($productInfo->variants); $v++) {

                  //  logger("v ".$v);

                    if ($productInfo->variants[$v]->checked) {

                        $mainImageID = null;
//                        if ($productInfo->variants[$v]->main_image && isset($imageIDArray[$productInfo->variants[$v]->main_image])) {
//                            $mainImageID = $imageIDArray[$productInfo->variants[$v]->main_image];
//                            $variantMainImageURLs[] = $productInfo->images[$productInfo->variants[$v]->main_image]->src;
//                        } else {
//                            $variantMainImageURLs[] = $variantMainImageURLs[0]; // use main product image as default variant image
//                        }
//                        if ($productInfo->variants[$v]->main_image && isset($imageIDArray[$productInfo->variants[$v]->main_image])) {
//                            fetch image id from db

                      //  logger("===images");
                        //logger($productInfo->images);
                       // logger("===variants ".$v);
                       // logger($productInfo->variants[$v]->main_image);

                        $main_src_from_variant = $productInfo->images[$productInfo->variants[$v]->main_image]->src;

                       // logger("========main_src_from_variant=======");
                       // logger($main_src_from_variant);

//                            $mainImageID = $imageIDArray[$productInfo->variants[$v]->main_image];

                       if(isset($imageSrcArray[$main_src_from_variant]))
                       {
                           $mainImageID = $imageSrcArray[$main_src_from_variant];
                       }

                      //  logger("mainImageID");
                      //  logger($mainImageID);

                        if ($productInfo->variants[$v]->main_image) {
                            $variantMainImageURLs[] = $productInfo->images[$productInfo->variants[$v]->main_image]->src;
                        } else {
                            $variantMainImageURLs[] = $variantMainImageURLs[0]; // use main product image as default variant image
                        }

                        $variantRecs[] = [
                            'product_id' => $productID,
                            'image_id' => $mainImageID,
                            'is_main' => false,
                            'title' => $productInfo->variants[$v]->title ? $productInfo->variants[$v]->title : $productInfo->title,
                            'in_stock' => $productInfo->variants[$v]->in_stock,
                            'inventory' => $productInfo->variants[$v]->inventory,
                            'prime_eligible' => $productInfo->prime_eligible,
                            'shipping_type' => $productInfo->shipping_type,
                            'source_id' => $productInfo->variants[$v]->source_id,
                            'source_price' => floatval($productInfo->variants[$v]->source_price),
                            'shipping_price' => floatval($productInfo->variants[$v]->shipping_price),
                            'shopify_price' => floatval($productInfo->variants[$v]->shopify_price),
                            'created_at' => date("Y-m-d H:i:s"),
                            'updated_at' => date("Y-m-d H:i:s")
                        ];

                        $shopifyVariant = [
                            'title' =>$productInfo->variants[$v]->title ? $productInfo->variants[$v]->title : $productInfo->title,
                            'sku' => $productInfo->variants[$v]->source_id,
                            'price' => floatval($productInfo->variants[$v]->shopify_price),
                            'inventory_quantity' => $productInfo->variants[$v]->inventory,
                            'inventory_management' => 'shopify',
                            'inventory_policy' => 'deny'
                        ];

                        $attsArray = [];

                        if ($productInfo->variants[$v]->attributes) {

                            $shopifyOptNum = 0;

                            foreach ($productInfo->variants[$v]->attributes as $attr) {
                                $attsArray[] = ['dimension' => $attr->dimension, 'value' => $attr->value];
                                $shopifyVariant['option'.++$shopifyOptNum] = $attr->value;
                            }
                        }

                        $variantAttsRecs[] = $attsArray;
                        $shopifyVariants[] = $shopifyVariant;
                    }
                }
            }

            // make unique variants
            $option = [];
            $newVariation = [];
            foreach ($shopifyVariants as $key => $val) {
                $opt = (@$val['option1']) ? $val['option1'] : '';
                $opt = (@$val['option2']) ? $opt.$val['option2'] : $opt;
                $opt = (@$val['option3']) ? $opt.$val['option3'] : $opt;

                if ($opt != '' && !in_array($opt, $option)) {
                    $newVariation[] = $val;
                }
                $option[] = $opt;
            }


//            $shopifyProduct['variants'] = $shopifyVariants;
            $shopifyProduct['variants'] = $newVariation;

            for ($v = 0; $v < count($variantRecs); $v++) {

                $variantID = DB::table('variants')->insertGetId($variantRecs[$v]);

                if (!$variantID) {
                    throw new \Exception('Failed to create variant record (index '.$v.').');
                }

                // Save attributes
                foreach ($variantAttsRecs[$v] as $attr) {
                    DB::table('attributes')->insert([
                        'variant_id' => $variantID, 'dimension' => $attr['dimension'], 'value' => $attr['value']
                    ]);
                }
            }

            $shopifyProduct['variants'] = array_slice($shopifyProduct['variants'], 0, 25);

            // Push product to Shopify


            //logger("=====shopifyProduct=====");
           // logger(json_encode($shopifyProduct));


            $productReq = $shop->api()->rest('POST', '/admin/api/'. env('SHOPIFY_API_VERSION') .'/products.json', ['product' => $shopifyProduct]);

            if ($productReq->errors) {
//                dump();
                throw new \Exception('Shopify API error: '.json_encode((array) $productReq->body));
            }

            if (!$productReq->body->product->id) {
                throw new \Exception('Could not retreieve product id from Shopify API');
            }

            if (empty($shopifyProduct['variants'])) {
                $variant = [
                    'variant' => [
                        'id' => $productReq->body->product->variants[0]->id,
                        'price' => $productInfo->shopify_price
                    ]
                ];

                $endPoint = '/admin/api/'. env('SHOPIFY_API_VERSION') .'/variants/'. $productReq->body->product->variants[0]->id . '.json';

                $variantReq = $shop->api()->rest('PUT', $endPoint, $variant);
            }

            $shopifyProductID = $productReq->body->product->id;
            $shopifyProductHandle = $productReq->body->product->handle;

            // Add Shopify ID to product record
            DB::table('products')
                ->where('id', $productID)
                ->update(['shopify_id' => $shopifyProductID, 'shopify_handle' => $shopifyProductHandle]);

            // Add product to collection
            if ($productInfo->collection) {

                $collectReq = $shop->api()->rest('POST', '/admin/api/'. env('SHOPIFY_API_VERSION') .'/collects.json', [
                    'collect' => [
                        'product_id' => $shopifyProductID, 'collection_id' => $productInfo->collection
                    ]
                ]);
            }
            // Assign images to product variants in Shopify
            if ($productReq->body->product->images && $productReq->body->product->variants) {

                $apiImages = $productReq->body->product->images;
                $apiVariants = $productReq->body->product->variants;

                for ($v = 0; $v < count($apiVariants); $v++) {

                    // Locate image index for current variant
                    for ($i = 0; $i < count($shopifyImages); $i++) {
                        if ($variantMainImageURLs[$v] == $shopifyImages[$i]['src']) {
                            $foundImageIndex = $i;
                            break;
                        }
                    }

                    // Set image id for this variant
                    if (isset($apiImages[$foundImageIndex])) {

                        $modVariant = [
                            'id' => $apiVariants[$v]->id,
                            'image_id' => $apiImages[$foundImageIndex]->id
                        ];


                        $variantReq = $shop->api()->rest('PUT', '/admin/api/'. env('SHOPIFY_API_VERSION') .'/variants/'.$apiVariants[$v]->id.'.json',
                            ['variant' => $modVariant]);
                    }

                    // Set Shopify ID for this variant
                    if (count($apiVariants) == 1) {
                        DB::table('variants')
                            ->where('product_id', $productID)
                            ->update(['shopify_id' => $apiVariants[$v]->id]);
                    } else {
                        DB::table('variants')
                            ->where('product_id', $productID)
                            ->where('source_id', $apiVariants[$v]->sku)
                            ->update(['shopify_id' => $apiVariants[$v]->id]);
                    }
                }
            }

            // increase product import counter
            $charge = DB::table('charges')->where('user_id', $shop->id)->where('status',
                "ACTIVE")->orderBy('created_at', 'desc')->first();
            if ($charge) {
                $charge_id = $charge->id;
                $counter = Counters::where('user_id', $shop->id)->where('charge_id', $charge_id)->where('status',
                    'active')->first();

                $affiliate = json_decode($request->post('affiliate'));
                if ($affiliate->status) {
                    $counter->affiliate_product_count = $counter->affiliate_product_count + 1;
                    $counter->affiliate_product_variant_count = $counter->affiliate_product_variant_count + count($productInfo->variants);
                } else {
                    $counter->regular_product_count = $counter->regular_product_count + 1;
                    $counter->regular_product_variant_count = $counter->regular_product_variant_count + count($productInfo->variants);
                }
                $counter->save();

             // logger('===== Product Add Counter :: Shop ID :: ' .$shop->id .' Product ID :: '.$productID.' ========');


            }
            //throw new \Exception(json_encode((array) $productReq));

            $isjobmsg = false;
            $jobmsg = 'Some additional images are being pushed in the background. Check product in few seconds.';
            // import review in background
            if ($review_row->is_import) {

                 if($productInfo->source=="Amazon"){
                     ImportProductReviewJob::dispatch($productID,$review_row);
                 }

                if($productInfo->source=="Walmart"){

                    $walmart_rating = 0;
                    if (isset($productInfo->rating)) {
                        $walmart_rating = $productInfo->rating;
                    }

                    $reviews = [];
                    if (isset($productInfo->product_reviews)) {
                        $reviews = $productInfo->product_reviews;
                    }

                    $reviews = json_encode($reviews);

                    ImportWalmartProductReviewJob::dispatch($productID,$review_row,$walmart_rating,$reviews);
                }

                $isjobmsg = true;
                $jobmsg = 'Product reviews are being pushed in the background. Check product in few seconds.';
            }

            // add remaining product images in product
            if ((count($shopifyImages) > 35)) {
                $remaining_imges = array_slice($shopifyImages, 35);
                AddProductImagesJob::dispatch($shop->id, $remaining_imges, $shopifyProductID);
                $jobmsg = 'Some additional images are being pushed in the background. Check product in few seconds.';
                $isjobmsg = true;
            }

            //is show review popup
            $is_show_review_popup = false;
            if (!$shop->is_clicked) {
                $productCount = DB::table('products')->where('user_id', $shop->id)->count();

                         //START::User has 45 days with app installed logic
                        $app_installd_date = $shop->created_at;
                        $date_app_start = new DateTime($app_installd_date);
                        $date_current = new DateTime("now");
                        $interval_app_installed = $date_app_start->diff($date_current);

                        $app_installd_day = $interval_app_installed->days;
                       //END::User has 45 days with app installed logic
                        logger("======shop name======");
                        logger($shop->name);

                        logger("======app_installd_day======");
                        logger($app_installd_day);


                $is_show_review_popup = ($productCount > 3);


                       // logger("======is_show_review_popup======");
                        //logger($is_show_review_popup);

            }


        } catch (\Exception $e) {
            DB::rollBack();
            $errors[] = $e->getMessage();
            return ['success' => false, 'errors' => $errors];
        }

        DB::commit();
        $product['view'] = 'https://'.$shop->name.'/products/'.$shopifyProductHandle;
        $product['edit'] = 'https://'.$shop->name.'/admin/products/'.$shopifyProductID;
        $product['my_product'] = '/products';
        $product['isjobmsg'] = $isjobmsg;
        $product['jobmsg'] = $jobmsg;
        $product['is_show_review_popup'] = $is_show_review_popup;
        $product['is_clicked'] = $shop->is_clicked;



        return ['success' => true, 'errors' => $errors, 'data' => $product];
    }

    public function ActiveFreePlan()
    {
        try {
            $shop = Auth::user();
            $plan = Plan::where('id', 1)->first();
//            create charge
            $charge = [
                "application_charge" => [
                    "name" => $plan->name,
                    "price" => 0.5,
                    "return_url" => env('APP_URL'),
                    "capped_amount" => $plan->capped_amount,
                    "terms" => $plan->terms,
                    "test" => $plan->test,
                ]
            ];
            $endPoint = '/admin/api/'.env('SHOPIFY_API_VERSION').'/application_charges.json';
            $result = $shop->api()->rest('POST', $endPoint, $charge);
            if (!$result->errors) {
                $charge_data = $result->body->application_charge;
                $old_charge = Charge::where('status', 'ACTIVE')->where('user_id', $shop->id)->first();
                if ($old_charge) {
                    $old_charge->status = 'CANCELLED';
                    $old_charge->cancelled_on = date('Y-m-d H:i:s');
                    $old_charge->save();
                }

                $charge = new Charge;
                $charge->charge_id = $charge_data->id;
                $charge->test = $charge_data->test;
                $charge->status = 'ACTIVE';
                $charge->name = $charge_data->name;
                $charge->type = 'ONETIME';
                $charge->price = $charge_data->price;
                $charge->trial_days = 0;
                $charge->billing_on = date("Y-m-d H:i:s", strtotime($charge_data->created_at));
                $charge->activated_on = date("Y-m-d H:i:s", strtotime($charge_data->created_at));
                $charge->created_at = date("Y-m-d H:i:s", strtotime($charge_data->created_at));
                $charge->updated_at = date("Y-m-d H:i:s", strtotime($charge_data->updated_at));
                $charge->plan_id = 1;
                $charge->user_id = $shop->id;
                $charge->save();

                $shop->plan_id = 1;
                $shop->save();
                return redirect('import');
            } else {
                dd($result);
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function getSearchCategory(Request $request)
    {

    }

    public function getSearchProduct(Request $request)
    {
        try {
            $searchData = $request->data;
            $errors = [];
            $products = [];

//            $queryString = http_build_query([
//                'api_key' => config('const.rainforest_api_key'),
//                'type' => 'search',
//                'amazon_domain' => $searchData['country'],
//                'search_term' => $searchData['category']
//            ]);
//
//            $result = $this->rainforestApiRequest($queryString);
//
//            dd($result);
            // fetch category

            $queryString = http_build_query([
                'api_key' => config('const.rainforest_api_key'),
                'type' => 'bestsellers',
                'url' => 'https://www.'.$searchData['country'].'/gp/bestsellers/'.$searchData['category']
            ]);

            $result = $this->rainforestApiRequest($queryString);
            if ($result['request_info']['success']) {
                $result = array_slice($result['bestsellers'], 0, 10);
            } else {
                $result = [];
            }
            return ['success' => true, 'errors' => $errors, 'products' => $result];
        } catch (\Exception $e) {
            return ['success' => false, 'errors' => $e->getMessage()];
        }
    }

    public function rainforestApiRequest($queryString)
    {
        $ch = curl_init(sprintf('%s?%s', 'https://api.rainforestapi.com/request', $queryString));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $api_result = curl_exec($ch);
        curl_close($ch);

# print the JSON response from Rainforest API
        $result = json_decode($api_result, true);

        return $result;
    }

    public function sendMailReportSuggestion(ProductReportSuggestionRequest $request)
    {

        try {

//            $params = [
//                'store_name'=> $request->store_name,
//                'store_email'=> $request->store_email,
//                'description' => $request->description,
//                'report_type' => $request->report_type,
//            ];


            $to_mail = env('MAIL_TO_ADDRESS');

            Mail::to($to_mail)->send(new ProductReportSuggestion($request->all()));

            return ['success' => true, "message" => "Email sent Successfully"];

        } catch (\Exception $e) {
            return ['success' => false, 'errors' => $e->getMessage()];
        }

    }


    public function checkValidatePlan(Request $request)
    {

        try {
            $shop = Auth::user();
            $errors = [];
            // get counter
            $curr_plan = $shop->plan_id;
            $charge = DB::table('charges')->where('status', 'ACTIVE')->where('user_id', $shop->id)->orderBy('created_at', 'desc')->first();
            $counter = Counters::where('user_id', $shop->id)->where('status', 'active')->where('charge_id',
                $charge->id)->first();
            $plan_detail = DB::table('plans')->where('id', $shop->plan_id)->first();
            if ($plan_detail->max_affiliate_product_import != 'unlimited') {
                $plan['err_msg'] = ($plan_detail->max_affiliate_product_import != 'unlimited' && ( $counter->affiliate_product_count  >= $plan_detail->max_affiliate_product_import)) ? 'With the '. $plan_detail->name .' plan you can import maximum ' . $plan_detail->max_affiliate_product_import . ' affiliate products, for more import <a href="/settings">please upgrade your plan</a>.' : '';
            }
            $plan['err_msg'] = ($counter->regular_product_count >= $plan_detail->max_regular_product_import) ? 'With the '. $plan_detail->name .' plan you can import maximum ' . $plan_detail->max_regular_product_import . ' regular products, for more import <a href="/settings">please upgrade your plan</a>.' : '';
            if( $curr_plan == 5 ){
                $plan['err_msg'] = ($counter->regular_product_count  >= $plan_detail->max_regular_product_import) ? 'With the '. $plan_detail->name .' plan you can import maximum ' . $plan_detail->max_regular_product_import . ' regular products.' : '';
            }

            return ['success' => true, 'errors' => $errors, 'plan' => $plan];

        } catch (\Exception $e) {
            return ['success' => false, 'errors' => $e->getMessage()];
        }

    }



}
