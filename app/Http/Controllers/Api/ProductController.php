<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Product;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Response;
use App\Traits\KeepaCustomAPITrait;
class ProductController extends Controller
{
    use KeepaCustomAPITrait;

   public  function checkAffiliate(Request $request){
       try{
           $shop_domain = $request->shop;
           $is_review = $request->is_review_class;

           if ($shop_domain) {
               $user = User::where('name', $shop_domain)->first();
               if ($user) {

                   $product = Product::where('user_id', $user->id)->where('shopify_handle', $request->handle)->first();

                   $reviews = [];
                   if ($product) {
                       $data['is_product'] = true;
                       $buttonstyle = json_decode($product->affiliate);
                       if ($buttonstyle->status) {
                           $data['is_product'] = true;
                           $data['redirect'] = $product->source_url;
                           $data['show_data'] = $buttonstyle->button_text;
                           $data['style'] = $buttonstyle->button_style;
                       } else {
                           $data['is_product'] = false;
                       }

                       if ($is_review) {
                           $db_reviews = (array) json_decode($product->reviews);
                           $reviews = $db_reviews;
                       }
                   } else {
                       $data['is_product'] = false;
                   }

                   return ($is_review && isset($reviews['is_show_front']) && $reviews['is_show_front']==true ) ?
                       Response::json([
                           'review' => \View::make('review', ["reviews" => $reviews])->render(), "data" => $data
                       ], 200) :
                       Response::json(["data" => $data], 200);
               }else{
                   $data['is_product'] = false;
                   return  Response::json(["data" => $data], 200);
               }
           }
       }catch (\Exception $e){
           return Response::json(["data" => $e], 422);
       }
   }





        public function keepaTrackProductWebhookSet(Request $request){

           try{

               logger("=============START :: Webhook :: SET Keepa Track Product===================");

              $input = $request->all();

              logger("Input");
              logger(json_encode($input));

               $res = $this->keepaSetWebhook();

               return  Response::json(['success' => true,"data" => $res, "message" => "Webhook set successfully." ], 200);

              logger("=============END :: Webhook ::SET Keepa Track Product===================");

           }catch(\Exception $e){
               logger("=============ERROR :: Webhook ::SET Keepa Track Product===================");
               return Response::json(["data" => $e],422);
           }


        }

    public function keepaTrackProductWebhook(Request $request){

        try{

            logger("=============START :: Webhook :: Keepa Track Product===================");

            $input = $request->all();

            logger("Input");
            logger(json_encode($input));


            return  Response::json(['success' => true,"message" => "Webhook set successfully." ], 200);

            logger("=============END :: Webhook :: Keepa Track Product===================");

        }catch(\Exception $e){
            logger("=============ERROR :: Webhook :: Keepa Track Product===================");
            return Response::json(["data" => $e],422);
        }


    }

    public function keepaListNames(Request $request){

        try{

            logger("=============START :: Keepa Named Lists===================");


             $listReq = $this->getKeepaListNames();

             logger(json_encode($listReq));


            return  Response::json(['success' => true, "data" => $listReq ,"message" => "Get Named Lists
 successfully." ], 200);

            logger("=============END ::  Keepa Named Lists===================");

        }catch(\Exception $e){
            logger("=============ERROR ::  Keepa Named Lists===================");
            return Response::json(["data" => $e],422);
        }

    }


    public function addProductTracking(Request $request){

        try{

            logger("=============START :: Keepa addProductTracking===================");

            $asin = $request->asin;
            $locale = $request->locale;

            if(!$locale) {
                return Response::json(['success' => false, "message" => "product Locale(US,IN etc.) required."], 200);
            }

            if($asin) {

                $mainDomainId = constant("Keepa\objects\AmazonLocale::$locale");

                $updateInterval = 1;

                $accessKey = config('const.keepa_api_key');

                $thresholdValues = [
                    [
                        "thresholdValue" => 999999999,
                        "domain" => $mainDomainId,
                        "csvType" => 0,
                        "isDrop" => true
                    ],
                    // track any price increases
                    [
                        "thresholdValue" => 0,
                        "domain" => $mainDomainId,
                        "csvType" => 0,
                        "isDrop" => false
                    ],
                ];

                $params = [
                    "asin" => $asin,
                    "mainDomainId" => $mainDomainId,
                    "ttl" => 0,
                    "expireNotify" => true,
                    "desiredPricesInMainCurrency" => false,
                    "updateInterval" => $updateInterval,
                    "metaData" => "Test Product Tracking",
                    "thresholdValues" => $thresholdValues,

                    // "notifyIf" => [],
                    "notificationType" => [ true, true, true, true, true, true, true ],
                    "individualNotificationInterval" => 0
                ];

                $apiUrl = "https://api.keepa.com/tracking/?key=".$accessKey."&"."type=add";

                $query_param = json_encode($params, true);

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => $apiUrl,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_POSTFIELDS => $query_param,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_HTTPHEADER => array(
                        "Content-Type: application/json",
                    ),
                ));

                $response = curl_exec($curl);

                $response = json_decode($response);

                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

                curl_close($curl);

                return Response::json(['success' => true,"data" => $response, "message" => "Product Tracking asin successfully."], 200);
            }else{
                return Response::json(['success' => false, "message" => "product ASIN required."], 200);
            }

            logger("=============END ::  Keepa addProductTracking===================");

        }catch(\Exception $e){
            logger("=============ERROR ::  Keepa addProductTracking===================");
            return Response::json(["data" => $e],422);
        }


    }


    public function getProductTracking(Request $request){

        try{

            logger("=============START :: Keepa getProductTracking===================");

            $asin = $request->asin;

              if($asin) {
                  $listReq = $this->keepaGetTrackProduct($asin);

                  logger(json_encode($listReq));

                  return Response::json(['success' => true,"data" => $listReq, "message" => "Get Tracking
 by asin successfully."], 200);
              }

            logger("=============END ::  Keepa getProductTracking===================");

        }catch(\Exception $e){
            logger("=============ERROR ::  Keepa getProductTracking===================");
            return Response::json(["data" => $e],422);
        }


    }




}
