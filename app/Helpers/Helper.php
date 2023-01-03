<?php

use App\Models\ShopifyShop;
use App\User;
use Illuminate\Support\Facades\Auth;

if ( ! function_exists( 'get_country_currency' ) ) {
    /**
     * get_country_currency.
     *
     * 237 countries.
     * Two-letter country code (ISO 3166-1 alpha-2) => Three-letter currency code (ISO 4217).
     */
    function get_country_currency($locale) {
        $array = [
            'AF' => 'AFN',
            'AL' => 'ALL',
            'DZ' => 'DZD',
            'AS' => 'USD',
            'AD' => 'EUR',
            'AO' => 'AOA',
            'AI' => 'XCD',
            'AQ' => 'XCD',
            'AG' => 'XCD',
            'AR' => 'ARS',
            'AM' => 'AMD',
            'AW' => 'AWG',
            'AU' => 'AUD',
            'AT' => 'EUR',
            'AZ' => 'AZN',
            'BS' => 'BSD',
            'BH' => 'BHD',
            'BD' => 'BDT',
            'BB' => 'BBD',
            'BY' => 'BYR',
            'BE' => 'EUR',
            'BZ' => 'BZD',
            'BJ' => 'XOF',
            'BM' => 'BMD',
            'BT' => 'BTN',
            'BO' => 'BOB',
            'BA' => 'BAM',
            'BW' => 'BWP',
            'BV' => 'NOK',
            'BR' => 'BRL',
            'IO' => 'USD',
            'BN' => 'BND',
            'BG' => 'BGN',
            'BF' => 'XOF',
            'BI' => 'BIF',
            'KH' => 'KHR',
            'CM' => 'XAF',
            'CA' => 'CAD',
            'CV' => 'CVE',
            'KY' => 'KYD',
            'CF' => 'XAF',
            'TD' => 'XAF',
            'CL' => 'CLP',
            'CN' => 'CNY',
            'HK' => 'HKD',
            'CX' => 'AUD',
            'CC' => 'AUD',
            'CO' => 'COP',
            'KM' => 'KMF',
            'CG' => 'XAF',
            'CD' => 'CDF',
            'CK' => 'NZD',
            'CR' => 'CRC',
            'HR' => 'HRK',
            'CU' => 'CUP',
            'CY' => 'EUR',
            'CZ' => 'CZK',
            'DK' => 'DKK',
            'DJ' => 'DJF',
            'DM' => 'XCD',
            'DO' => 'DOP',
            'EC' => 'ECS',
            'EG' => 'EGP',
            'SV' => 'SVC',
            'GQ' => 'XAF',
            'ER' => 'ERN',
            'EE' => 'EUR',
            'ET' => 'ETB',
            'FK' => 'FKP',
            'FO' => 'DKK',
            'FJ' => 'FJD',
            'FI' => 'EUR',
            'FR' => 'EUR',
            'GF' => 'EUR',
            'TF' => 'EUR',
            'GA' => 'XAF',
            'GM' => 'GMD',
            'GE' => 'GEL',
            'DE' => 'EUR',
            'GH' => 'GHS',
            'GI' => 'GIP',
            'GR' => 'EUR',
            'GL' => 'DKK',
            'GD' => 'XCD',
            'GP' => 'EUR',
            'GU' => 'USD',
            'GT' => 'QTQ',
            'GG' => 'GGP',
            'GN' => 'GNF',
            'GW' => 'GWP',
            'GY' => 'GYD',
            'HT' => 'HTG',
            'HM' => 'AUD',
            'HN' => 'HNL',
            'HU' => 'HUF',
            'IS' => 'ISK',
            'IN' => 'INR',
            'ID' => 'IDR',
            'IR' => 'IRR',
            'IQ' => 'IQD',
            'IE' => 'EUR',
            'IM' => 'GBP',
            'IL' => 'ILS',
            'IT' => 'EUR',
            'JM' => 'JMD',
            'JP' => 'JPY',
            'JE' => 'GBP',
            'JO' => 'JOD',
            'KZ' => 'KZT',
            'KE' => 'KES',
            'KI' => 'AUD',
            'KP' => 'KPW',
            'KR' => 'KRW',
            'KW' => 'KWD',
            'KG' => 'KGS',
            'LA' => 'LAK',
            'LV' => 'EUR',
            'LB' => 'LBP',
            'LS' => 'LSL',
            'LR' => 'LRD',
            'LY' => 'LYD',
            'LI' => 'CHF',
            'LT' => 'EUR',
            'LU' => 'EUR',
            'MK' => 'MKD',
            'MG' => 'MGF',
            'MW' => 'MWK',
            'MY' => 'MYR',
            'MV' => 'MVR',
            'ML' => 'XOF',
            'MT' => 'EUR',
            'MH' => 'USD',
            'MQ' => 'EUR',
            'MR' => 'MRO',
            'MU' => 'MUR',
            'YT' => 'EUR',
            'MX' => 'MXN',
            'FM' => 'USD',
            'MD' => 'MDL',
            'MC' => 'EUR',
            'MN' => 'MNT',
            'ME' => 'EUR',
            'MS' => 'XCD',
            'MA' => 'MAD',
            'MZ' => 'MZN',
            'MM' => 'MMK',
            'NA' => 'NAD',
            'NR' => 'AUD',
            'NP' => 'NPR',
            'NL' => 'EUR',
            'AN' => 'ANG',
            'NC' => 'XPF',
            'NZ' => 'NZD',
            'NI' => 'NIO',
            'NE' => 'XOF',
            'NG' => 'NGN',
            'NU' => 'NZD',
            'NF' => 'AUD',
            'MP' => 'USD',
            'NO' => 'NOK',
            'OM' => 'OMR',
            'PK' => 'PKR',
            'PW' => 'USD',
            'PA' => 'PAB',
            'PG' => 'PGK',
            'PY' => 'PYG',
            'PE' => 'PEN',
            'PH' => 'PHP',
            'PN' => 'NZD',
            'PL' => 'PLN',
            'PT' => 'EUR',
            'PR' => 'USD',
            'QA' => 'QAR',
            'RE' => 'EUR',
            'RO' => 'RON',
            'RU' => 'RUB',
            'RW' => 'RWF',
            'SH' => 'SHP',
            'KN' => 'XCD',
            'LC' => 'XCD',
            'PM' => 'EUR',
            'VC' => 'XCD',
            'WS' => 'WST',
            'SM' => 'EUR',
            'ST' => 'STD',
            'SA' => 'SAR',
            'SN' => 'XOF',
            'RS' => 'RSD',
            'SC' => 'SCR',
            'SL' => 'SLL',
            'SG' => 'SGD',
            'SK' => 'EUR',
            'SI' => 'EUR',
            'SB' => 'SBD',
            'SO' => 'SOS',
            'ZA' => 'ZAR',
            'GS' => 'GBP',
            'SS' => 'SSP',
            'ES' => 'EUR',
            'LK' => 'LKR',
            'SD' => 'SDG',
            'SR' => 'SRD',
            'SJ' => 'NOK',
            'SZ' => 'SZL',
            'SE' => 'SEK',
            'CH' => 'CHF',
            'SY' => 'SYP',
            'TW' => 'TWD',
            'TJ' => 'TJS',
            'TZ' => 'TZS',
            'TH' => 'THB',
            'TG' => 'XOF',
            'TK' => 'NZD',
            'TO' => 'TOP',
            'TT' => 'TTD',
            'TN' => 'TND',
            'TR' => 'TRY',
            'TM' => 'TMT',
            'TC' => 'USD',
            'TV' => 'AUD',
            'UG' => 'UGX',
            'UA' => 'UAH',
            'AE' => 'AED',
            'GB' => 'GBP',
            'US' => 'USD',
            'UM' => 'USD',
            'UY' => 'UYU',
            'UZ' => 'UZS',
            'VU' => 'VUV',
            'VE' => 'VEF',
            'VN' => 'VND',
            'VI' => 'USD',
            'WF' => 'XPF',
            'EH' => 'MAD',
            'YE' => 'YER',
            'ZM' => 'ZMW',
            'ZW' => 'ZWD',
        ];
        return $array[strtoupper($locale)];
    }
}

if (!function_exists('noImagePathH')) {
    /**
     * @return mixed
     */
    function noImagePathH()
    {
        return asset('images/static/no-image-box.png');
    }
}

if (!function_exists('rainforestApiRequest')) {
    /**
     * @return mixed
     */
    function rainforestApiRequest($queryString)
    {
        $ch = curl_init(sprintf('%s?%s', 'https://api.rainforestapi.com/request', $queryString));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $api_result = curl_exec($ch);
        curl_close($ch);

# print the JSON response from Rainforest API
        $result =  json_decode($api_result, true);

        return $result;
    }
}


if (!function_exists('LatestExchangeRates')) {
    /**
     * @return mixed
     */
    function LatestExchangeRates($base, $target)
    {
        try{
          //  logger('=================== LatestExchangeRates ==============');
            $rawdata = file_get_contents("https://data.fixer.io/api/latest?access_key=3175c637bf1d91ef7dedd6640654bb1a&base=$base&symbols=$target");

            $data = json_decode($rawdata, true);

            return (@$data['rates'][$target]) ? $data['rates'][$target] : 0.00;
        }catch(\Exception $e){
            dd($e);
        }
    }
}

if (!function_exists('amazonKeepaTimeMinutesToUnixTime')) {
    /**
     * @return mixed
     */
    function amazonKeepaTimeMinutesToUnixTime($amazonKeepaMinutes)
    {
        try{
            return (21564000 + $amazonKeepaMinutes) * 60;
        }catch(\Exception $e){
            dd($e);
        }
    }
}


if (!function_exists('getShopDataFromAPI')) {
    function getShopDataFromAPI($user, $fields)
    {
       // logger('=========== START:: getShopDataFromAPI ===========');
        try {
            $shop = Auth::user();
            $parameter['fields'] = $fields;

            $shop_result = $shop->api()->rest('GET', 'admin/api/'.env('SHOPIFY_API_VERSION').'/shop.json', $parameter);

            if (!$shop_result->errors) {
                return $shop_result->body->shop;
            }

        } catch (\Exception $e) {
            logger('=========== ERROR:: getShopDataFromAPI ===========');
            logger(json_encode($e));
        }
    }
}


if (!function_exists('getShopData')) {
    function getShopData($user_id)
    {
        try {

           // logger('=========== START:: getShopData ===========');

           // logger("Shop ID :: ". $user_id);

            $data = ShopifyShop::select('name','email','domain','currency','country_code')->where('user_id',$user_id)->first();

            return $data;

        } catch (\Exception $e) {
            logger('=========== ERROR:: getShopData ===========');
           // logger(json_encode($e));
        }
    }
}

if (!function_exists('date_range')) {
    function date_range($first, $last, $step = '+1 day', $output_format = 'M Y')
    {

        $dates = array();
        $current = strtotime($first);
        $last = strtotime($last);

        while ($current <= $last) {

            $dates[] = date($output_format, $current);
            $current = strtotime($step, $current);
        }

        return $dates;
    }
}

if (!function_exists('array2ul')) {
    function array2ul($array)
    {
        $output = '<ul>';
        foreach ($array as $key => $value) {
            $function = is_array($value) ? __FUNCTION__ : 'htmlspecialchars';
            $output .= '<li><b>' . $key . ':</b> <i>' . $function($value) . '</i></li>';
        }
        return $output . '</ul>';
    }
}



if (!function_exists('jsonFileDataFetch')) {
        function jsonFileDataFetch()
        {

                    $apiTestURL = "https://amazon-dropshipper.test/data/walmart.json";

                    $arrContextOptions=array(
                        "ssl"=>array(
                            "verify_peer"=>false,
                            "verify_peer_name"=>false,
                        ),
                    );

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_URL,$apiTestURL);
                    $data = curl_exec($ch);

                    $data = json_decode($data, true);

                    return $data;

        }
}


if (!function_exists('getShopCollectionsCategoryData')) {
    function getShopCollectionsCategoryData($user)
    {
        //logger('=========== START:: getCollectionsData ===========');
        try {

            $shop = $user;

            $errors = [];

            $collections = [];

            $apiRequest = $shop->api()->rest('GET', '/admin/api/'. env('SHOPIFY_API_VERSION') .'/custom_collections.json', ['limit' => 250]);

            if (isset($apiRequest->body->custom_collections)) {
                $collections = $apiRequest->body->custom_collections;
            }

            return $collections;


        } catch (\Exception $e) {
            logger('=========== ERROR:: getCollectionsData ===========');
            logger(json_encode($e));
        }
    }
}


if (!function_exists('getShopTagsData')) {
    function getShopTagsData($user)
    {
      //  logger('=========== START:: getShopTagsData ===========');
        try {

            $shop = $user;

            $errors = [];

            $tags = [];

            $query ='{
                      shop{
                        productTags(first: 100){
                          edges{
                            node
                          }
                        }
                      }
                    }';


            $apiRequest = $shop->api()->graph($query);


            //logger("===================Tags Shopify Res=========================");
            //logger(json_encode($apiRequest));

            if(!$apiRequest->errors) {

               // logger("tags find success");

                if (isset($apiRequest->body->shop->productTags->edges)) {
                    $tags = $apiRequest->body->shop->productTags->edges;

                }
            }
            return $tags;


        } catch (\Exception $e) {
            logger('=========== ERROR:: getShopTagsData ===========');
            logger(json_encode($e));
        }
    }
}



if (!function_exists('webhooksList')) {
    function webhooksList($shop_id)
    {
        try {

            logger('================= START::webhooks List =================');

            $result = [];

            $shop = User::where('id', $shop_id)->first();

            $webhookReq = $shop->api()->rest('GET', '/admin/api/'.env('SHOPIFY_API_VERSION').'/webhooks.json');

            logger("===============webhooks :: Result=================");
            logger(json_encode($webhookReq));
            logger('================= END::  webhooks List =================');

            if(!$webhookReq->errors){
                if(isset($webhookReq->body->webhooks)) {
                    $result = $webhookReq->body->webhooks;
                }
            }

            return $result;

        } catch (\Exception $e) {
            logger('================= ERROR::  webhooks List =================');
            logger($e->getMessage());
            return true;
        }
    }
}

