<?php

namespace App\Traits;

use Exception;
use Response;
use DB;

trait KeepaCustomAPITrait
{


    public function testt()
    {

        return "hello";

    }


    public function keepaSetWebhook()
    {

        try {
            logger("=============START :: Traits :: keepaSetWebhook===================");

            $accessKey = config('const.keepa_api_key');

            $webhookURL = route('keepa.webhook');

            $apiUrl = "https://api.keepa.com/tracking/?key=".$accessKey."&"."type=webhook&url=".$webhookURL; //Get Tracking

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $apiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                ),
            ));

            $response = curl_exec($curl);

            logger("Res :: set-Webhook");
            logger(json_encode($response));


            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            logger('HTTP code: '.$httpcode);

            curl_close($curl);

            return $httpcode;

        } catch (Exception $e) {
            logger("=============ERROR :: Traits :: keepaSetWebhook===================");
            return Response::json(["data" => $e], 422);
        }


    }


    public function keepaAddProductTracking($user_id, $param)
    {

        try {

            logger("=============START :: Traits :: add Track Product===================");

            $is_set_webhok = DB::table('keepa_webhook')->select('is_set_webhook')->where('user_id',
                $user_id)->value("is_set_webhook");

            if (!$is_set_webhok && $is_set_webhok !== 0) {

                $webhookRes = $this->keepaSetWebhook();

                if ($webhookRes == 200 || $webhookRes == 400) {
                    DB::table('keepa_webhook')->insert([
                        'is_set_webhook' => 1,
                        'user_id' => $user_id,
                        'created_at' => date("Y-m-d H:i:s"),
                        'updated_at' => date("Y-m-d H:i:s")
                    ]);
                }
            }

            $asin = $param['asin'];
            $mainDomainId = $param['mainDomainId'];
            $updateInterval = $param['updateInterval'];

            logger("updateInterval". $updateInterval);

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

            logger("tracking param");
            logger(json_encode($params));


            $apiUrl = "https://api.keepa.com/tracking/?key=".$accessKey."&"."type=add";

            $query_param = json_encode($params, true);

            logger("query Param cred");
            logger($query_param);

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

            logger("===Response===");
            logger(json_encode($response));

            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            logger('HTTP code: '.$httpcode);

            curl_close($curl);

            return true;

            logger("=============END :: Traits ::  add Track Product===================");

        } catch (Exception $e) {
            logger("=============ERROR :: Traits ::  add Track Product===================");
            return Response::json(["data" => $e], 422);
        }


    }




    public function getKeepaListNames()
    {

        try {
            logger("=============START :: Traits :: ListNames===================");

            $accessKey = config('const.keepa_api_key');

            $webhookURL = route('keepa.webhook');

            $apiUrl = "https://api.keepa.com/tracking/?key=".$accessKey."&type=listNames"; //Get Tracking

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $apiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                ),
            ));

            $response = curl_exec($curl);

            //dd($response);
            return json_decode($response);

            logger("Res :: ListNames");
            logger(json_encode($response));


            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            logger('HTTP code: '.$httpcode);

            curl_close($curl);

            return $httpcode;

        } catch (Exception $e) {
            logger("=============ERROR :: Traits :: ListNames===================");
            return Response::json(["data" => $e], 422);
        }


    }

    public function keepaGetTrackProduct($asin)
    {

        try {
            logger("=============START :: Traits :: Track list===================");

            $accessKey = config('const.keepa_api_key');

            $webhookURL = route('keepa.webhook');

            $apiUrl = "https://api.keepa.com/tracking/?key=".$accessKey."&"."type=get&asin=".$asin; //Get Tracking

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $apiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                ),
            ));

            $response = curl_exec($curl);

            return json_decode($response);

            logger("Res :: ListNames");
            logger(json_encode($response));


            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            logger('HTTP code: '.$httpcode);

            curl_close($curl);

            return $httpcode;

        } catch (Exception $e) {
            logger("=============ERROR :: Traits :: Track list===================");
            return Response::json(["data" => $e], 422);
        }


    }




}
