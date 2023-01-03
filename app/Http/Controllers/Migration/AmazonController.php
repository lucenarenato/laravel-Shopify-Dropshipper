<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use App\Jobs\AmazonMigrationJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use LukeTowers\ShopifyPHP\Shopify;
use App\Traits\AutoUpdate;
use App\User;

class AmazonController extends Controller
{
     use AutoUpdate;
    public function index($table){
        try{

          $id = 70437;
          $user = User::find($id);

          // dd($user);
          $endpoint = '/admin/api/application_charges.json';
          $result = $user->api()->rest('GET', $endpoint);
          dd($result);
            // $from_currency = 'GBP';
            // $to_currency = 'JPY';
            // $convertTarget = $from_currency . ',' . $to_currency;
            // $latestRates = LatestExchangeRates('USD', $convertTarget);
            //     dd($latestRates);
          // $user = \Auth::user();
          // $endPoint = 'admin/api/' . env('SHOPIFY_API_VERSION') . '/webhooks.json';
          // $result = $user->api()->rest('GET', $endPoint, []);
          // dd($result);
          // SELECT count(*) FROM `products` WHERE `updated_at` BETWEEN '2021-01-22 00:00:00.000000' AND '2021-01-22 23:59:59' ORDER BY `created_at` DESC
          // $products = DB::table('products')->whereBetween('updated_at', ['2021-01-22 00:00:00', '2021-01-22 23:59:59'])->limit(1)->get();
          // foreach ($products as $key => $value) {
          //    $res = AmazonMigrationJob::dispatch($value->id);
          // }
          // dd('111');
        	// [0,50][50,50][100,50][150,50][200,50]
           // $new_db = DB::connection('mysql');
           // $live_db = DB::connection('mysql2');
           //  // $is_user = $new_db->select("SELECT * FROM users WHERE id=65183");
           // $subscribed_user = $new_db->select("SELECT * FROM `after_july_2020` WHERE `old_migrated` = 0 AND `new_migrated` = 0 AND `is_mismatch` = 0 AND `mismatch_store` IS NULL limit 0,50");
           // dd($subscribed_user);
           // //  // after july 2020
           // $date = '1-07-2020';
           // $start_date = date('Y-m-d', strtotime('1-07-2020'));
           // // $end_date = date('Y-m-d');
           // $user = $live_db->select("SELECT * FROM usersettings WHERE status = 1 AND added_at <= '$start_date'");
           // dd($user);
           // foreach ($ as $key => $value) {
           //   # code...
           // }
			        // $subscribed_user = $new_db->select("SELECT * FROM unsubscribed_user limit 50, 1");
              // dd($subscribed_user);

            // $user = $live_db->select("SELECT * FROM usersettings WHERE store_name='retromaggie.myshopify.com'");
            // // dd($user);
            // dump($user[0]);
            // $store_id = $user[0]->id;
            // $lvproduct = $live_db->select("SELECT count(*) FROM products WHERE store_id = $store_id");
            //  // $lvproduct = $live_db->select("SELECT count(*) FROM products WHERE store_id = $store_id  AND status = 1");
            // dd($lvproduct);

            // $store_id = $user[0]->id;
            // $single_payment = $live_db->select("SELECT * FROM app_payments WHERE status = 'active' AND store_id = $store_id ORDER BY id DESC LIMIT 1");
            // dd($single_payment[0]);
            // dd(date('Y-m-d H:i:s', strtotime($single_payment[0]->created_at)));


             // $lvproduct = $live_db->select("SELECT * FROM products WHERE store_id = 63901 AND status = 0");
             // if (!empty($lvproduct)) {
             //    foreach ($lvproduct as $key => $val) {

             //    }
             //  }

           // $after_2020_user = $new_db->select("SELECT * FROM `after_july_2020` WHERE `old_migrated` = 0 AND `new_migrated` = 0 AND `is_mismatch` = 1 AND `mismatch_store` IS NULL limit 0,1");
           // foreach ($after_2020_user as $key => $value) {
           //     $products = $new_db->select("SELECT count(*) as count FROM `products` WHERE `user_id` = $value->new_id");
           //     dd($products[0]->count);
           // }
             // $res = AmazonMigrationJob::dispatch($table);

        }catch( \Exception $e ){
            dd($e);
        }
    }

}
