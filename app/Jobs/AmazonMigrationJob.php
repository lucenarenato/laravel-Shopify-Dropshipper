<?php

namespace App\Jobs;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use LukeTowers\ShopifyPHP\Shopify;
use Osiset\ShopifyApp\Storage\Models\Charge;
use Osiset\ShopifyApp\Storage\Models\Plan;
use App\Product;
use App\Traits\AutoUpdate;

class AmazonMigrationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use AutoUpdate;
    private $table = '';
    private $store_id = '';
    private $user_store_id = '';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($table)
    {
        //test stores
        // 1. thesuperpetstore.myshopify.com => 60439
        // 2. passportstore.myshopify.com => 65171

        $this->table = $table;
        // $this->store_id = '65197';
        // $this->user_store_id = '66132';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $new_db = DB::connection('mysql');
        $live_db = DB::connection('mysql2');
        $table = $this->table;
        $this->$table($live_db, $new_db);
        // $this->$table();

    }

    // // after july 2020
    // public function user1($live_db, $new_db)
    // {
    //     try {

    //         \Log::info('================= START:: User Migration ===============');

    //         // after july 2020
    //        $date = '1-07-2020';
    //        $start_date = date('Y-m-d', strtotime('1-07-2020'));
    //        $end_date = date('Y-m-d');
    //        $user = $live_db->select("SELECT * FROM usersettings WHERE status = 1 AND added_at BETWEEN '$start_date' AND '$end_date'");

    //        foreach ($user as $key => $value) {
    //             $afterJuly = [];
    //             $afterJuly['old_id'] = $value->id;
    //             $afterJuly['name'] = $value->store_name;
    //             $new_user = User::where('name', $value->store_name)->first();
    //             if( $new_user ){
    //                 $afterJuly['new_id'] = $new_user->id;

    //                 $products = Product::where('user_id', $new_user->id)->count();
    //                 if( $products > 0 ){
    //                     $afterJuly['old_migrated'] = 1;
    //                 }
    //             }else{
    //                  $afterJuly['new_id'] = $value->id;
    //             }

    //             if(  (int)$afterJuly['old_id'] != (int)$afterJuly['new_id'] ){
    //                  $afterJuly['is_mismatch'] = 1;
    //             }
    //             $same_id_user = User::where('id', $value->id)->first();
    //             if( $same_id_user ){
    //                 if( $value->store_name != $same_id_user->name ){
    //                     $afterJuly['mismatch_store'] = $same_id_user->name;
    //                 }
    //             }


    //              $new_db->table('after_july_2020')->insert($afterJuly);
    //        }

    //         \Log::info('================= END:: User Migration ===============');
    //     } catch (\Exception $e) {
    //         \Log::info($e);
    //     }


    //     // first migration query = ( $subscribed_user = $new_db->select("SELECT count(*) FROM `after_july_2020` WHERE `old_migrated` = 0 AND `is_mismatch` = 0 AND `mismatch_store` IS NULL");)
    //     // secone_migration_query = $after_2020_user = $new_db->select("SELECT * FROM `after_july_2020` WHERE `old_migrated` = 0 AND `new_migrated` = 0 AND `is_mismatch` = 1 AND `mismatch_store` IS NULL limit 0,50");
    //third migration query = $after_2020_user = $new_db->select("SELECT * FROM `after_july_2020` WHERE `old_migrated` = 1 AND `new_migrated` = 0 AND `is_mismatch` = 0 AND `mismatch_store` IS NULL");
    //fourth migration query = $after_2020_user = $new_db->select(SELECT * FROM `after_july_2020` WHERE `old_migrated` = 0 AND `new_migrated` = 0 AND `is_mismatch` = 0 AND `mismatch_store` IS NOT NULL");
    // fifth migration query = SELECT * FROM `after_july_2020` WHERE `old_migrated` = 0 AND `new_migrated` = 0 AND `is_mismatch` = 1 AND `mismatch_store` IS NOT NULL
    // SELECT * FROM `after_july_2020` WHERE `old_migrated` = 0 AND `new_migrated` = 0 AND `is_mismatch` = 0 AND `mismatch_store` IS NOT NULL
    // }

    // after july 2020
    // public function user($live_db, $new_db)
    // {
    //     try {

    //         \Log::info('================= START:: User Migration ===============');

    //        $after_2020_user = $new_db->select("SELECT * FROM `after_july_2020` WHERE `old_migrated` = 1 AND `new_migrated` = 0 AND `is_mismatch` = 0 AND `mismatch_store` IS NULL");
    //        foreach ($after_2020_user as $key => $value) {
    //            $products = $new_db->select("SELECT count(*) as count FROM `users` WHERE `id` = $value->new_id");
    //            logger($products[0]->count);
    //            if( $products[0]->count > 0 ){
    //                 logger($value->new_id);
    //                 logger($products[0]->count);
    //                 $new_db->update("UPDATE after_july_2020 SET new_migrated = 1 WHERE `id` = $value->id");
    //            }
    //        }

    //         \Log::info('================= END:: User Migration ===============');
    //     } catch (\Exception $e) {
    //         \Log::info($e);
    //     }
    // }

    public function user($live_db, $new_db)
    {
        try {

            \Log::info('================= START:: User Migration ===============');
            // $after_2020_user = $new_db->select("SELECT * FROM `after_july_2020` WHERE `old_migrated` = 0 AND `new_migrated` = 0 AND `is_mismatch` = 0 AND `mismatch_store` IS NOT NULL");

            // foreach( $after_2020_user as $skey=>$sval ){
            // logger($sval->name);
            $store = 'beautypowerlllp.myshopify.com';
            // $user_settings = $live_db->select("SELECT * FROM usersettings WHERE store_name='".$sval->name."'");
            $user_settings = $live_db->select("SELECT * FROM usersettings WHERE store_name='".$store."'");

            // if( !empty($user_settings) ){
            $suser = $user_settings[0];

            $user['id'] = $suser->id;
            $user['name'] = $suser->store_name;
            $user['email'] = 'shop@'.$suser->store_name;
            $user['password'] = $suser->access_token;
            $user['created_at'] = $suser->added_at;
            $user['updated_at'] = date('Y-m-d H:i:s');
            $user['is_migrated'] = 1;
            $user['plan_id'] = $suser->plan;

            $is_user = $new_db->select("SELECT * FROM users WHERE name = '" . $suser->store_name . "'");
            // logger($is_user);
            if( empty($is_user) ){
                $new_db->table('users')->insert($user);

                $newuser = $new_db->select("SELECT * FROM users WHERE name = '" . $suser->store_name . "'");
                // logger($newuser);
                // logger($newuser[0]->id);
                $this->store_id = $newuser[0]->id;
                $this->user_store_id = $newuser[0]->id;


                // $this->charges($live_db, $new_db);
                // $this->product($live_db, $new_db);
                // $this->counters($live_db, $new_db);
                // $this->affiliates($live_db, $new_db);
                // $this->ScriptTag();
                // $this->Webhook();

                // $new_db->update("UPDATE after_july_2020 SET new_migrated = 1 WHERE id = ".$sval->id);
            }else{
                $this->store_id = $suser->id;
                $this->user_store_id = $is_user[0]->id;

                logger($this->store_id);
                logger($this->user_store_id);
            }
            // }else{
            //     // $new_db->update("UPDATE subscribed_user SET is_migrated = 0 WHERE id = ".$sval->id);
            // }

            $this->charges($live_db, $new_db);
            $this->product($live_db, $new_db);
            $this->counters($live_db, $new_db);
            $this->affiliates($live_db, $new_db);
            $this->ScriptTag();
            $this->Webhook();
            // }


            \Log::info('================= END:: User Migration ===============');
        } catch (\Exception $e) {
            \Log::info($e);
        }
    }

    public function charges($live_db, $new_db)
    {
        // app_payments
        \Log::info('================= START:: charges Migration ===============');
        try {
            $store_id = $this->store_id;
//            $app_payment = $live_db->select("SELECT DISTINCT store_id FROM app_payments WHERE store_id = $store_id");

//            if (!empty($app_payment)) {
//                foreach ($app_payment as $key => $val) {
//                    $single_payment = $live_db->select("SELECT * FROM app_payments WHERE store_id = ".$val->store_id.' ORDER BY id DESC LIMIT 1');
            $single_payment = $live_db->select("SELECT * FROM app_payments WHERE status = 'active' AND store_id = $store_id ORDER BY id DESC LIMIT 1");
            if (!empty($single_payment)) {
                $result = $single_payment[0];

                if( $result->status == 'active' ){
//                            $user = $live_db->select("SELECT * FROM usersettings WHERE id = ".$val->store_id);
                    $user = $live_db->select("SELECT * FROM usersettings WHERE id = ".$store_id);
                    $user = $user[0];

                    $plan = $new_db->select("SELECT * FROM plans WHERE name ='" . $result->plan_name . "'");
                    $plan = (@$plan[0]) ? $plan[0] : 0;
                    \Log::info($result->id);

                    $data['id'] = $result->id;
                    $data['user_id'] = $this->user_store_id;
                    $data['charge_id'] = $result->pay_id;
                    $data['test'] = 0;
                    $data['status'] = strtoupper($result->status);
                    $data['name'] = $result->plan_name;
                    $data['type'] = 'RECURRING';
                    $data['price'] = $result->price;
                    $data['trial_days'] = $result->trial_days;

                    if ($result->trial_ends_on != 0) {
                        $data['trial_ends_on'] = date("Y-m-d", strtotime($result->trial_ends_on));
                        $data['billing_on'] = date("Y-m-d", strtotime($result->trial_ends_on));
                    }

                    $data['activated_on'] = $user->current_plan_date;
//                $data['cancelled_on'] = '';
//                $data['expires_on'] = '';
                    $data['plan_id'] = (@$plan->id) ? $plan->id : 0;
//                $data['description'] = '';
//                $data['reference_charge'] = '';
                    $data['created_at'] = date("Y-m-d", strtotime($result->created_at));
                    $data['updated_at'] = date('Y-m-d H:i:s');

                    $new_db->table('charges')->insert($data);
                }else{
                    $this->ActiveFreePlan($live_db, $store_id, $new_db);
                }
            }else{
                $this->ActiveFreePlan($live_db, $store_id, $new_db);
            }

//                }
//            }
            \Log::info('================= END:: charges Migration ===============');
        } catch (\Exception $e) {
            \Log::info($e);
        }
    }

    public function product($live_db, $new_db)
    {
        try {
            \Log::info('================= START:: Product Migration ===============');
            $store_id = $this->store_id;

            $lvproduct = $live_db->select("SELECT * FROM products WHERE store_id = $store_id AND status = 1");

            if (!empty($lvproduct)) {
                foreach ($lvproduct as $key => $val) {
                    \Log::info($val->shopify_prod_id);

                    if( $val->status == 1 ){
                        $url = $val->prod_url;

                        $source = (strpos($url, 'amazon') == true) ? 'amazon' : 'walmart';

                        if ($source == 'amazon') {
                            $amazon_meta_query = $live_db->select("SELECT * FROM products_meta WHERE store_id = ".$val->store_id." AND parent_id = 0 AND product_url = '".$val->prod_url."'");
                            $lvproMeta = $amazon_meta_query;
//                        $lvproMeta[] = ($source == 'amazon') ? $amazon_meta_query : $walmart_meta_query;
                            $locale = $this->getLocale($val->prod_url);

//                        \Log::info($lvproMeta);
                            if (!empty($lvproMeta)) {

                                $mainVariant = $lvproMeta[0];

//                                if ($mainVariant->aff_url != '') {
                                if ($val->prod_aff_url != '') {
                                    $aff_btn_txt = ($mainVariant->button_text != '') ? $mainVariant->button_text : 'Buy from Amazon';
                                } else {
                                    $aff_btn_txt = 'Buy from Amazon';
                                }
                                $affiliate = [
                                    'country' => $locale,
//                                    'status' => ($mainVariant->aff_url != '') ? true : false,
                                    'status' => ($val->prod_aff_url != '') ? true : false,
                                    'button_style' => 0,
                                    'button_text' => $aff_btn_txt
                                ];
                                $sh_product = $this->getShopifyProduct($val->store_id, $live_db);

                                $sh_handle = '';
                                $sh_title = '';
                                if ($sh_product != false) {
                                    $sh_handle = $sh_product->handle;
                                    $sh_title = $sh_product->title;
                                }

                                // add product
                                $productRec = [
                                    'user_id' => $this->user_store_id,
                                    'shopify_id' => $val->shopify_prod_id,
                                    'shopify_handle' => $sh_handle,
                                    'collection_id' => 0,
                                    'type' => '',
                                    'locale' => $locale,
                                    'source' => ucwords($source),
                                    'source_url' => ($mainVariant->aff_url != '') ? $mainVariant->aff_url : $mainVariant->product_url,
                                    'description' => $mainVariant->description,
                                    'affiliate' => json_encode($affiliate),
                                    'saller_ranks' => '',
                                    'created_at' => $val->created_at,
                                    'updated_at' => $val->updated_at,
                                    'is_migrated' => 1
                                ];
                                $lcl_product_id = $new_db->table('products')->insertGetId($productRec);

                                // add main image
                                $imageRec = [
                                    'product_id' => $lcl_product_id,
                                    'url' => $mainVariant->featured_image,
                                    'created_at' => date("Y-m-d H:i:s"),
                                    'updated_at' => date("Y-m-d H:i:s")
                                ];
                                $imageID = DB::table('images')->insertGetId($imageRec);

                                $inventory = $this->getInventory($sh_product, $mainVariant->selected_options);

                                //                        // add main variant
                                $variantRecs = [
                                    'product_id' => $lcl_product_id,
                                    'image_id' => $imageID,
                                    'is_main' => true,
                                    'title' => $sh_title,
                                    'in_stock' => 0,
                                    'inventory' => $inventory,
                                    'prime_eligible' => ($mainVariant->is_prime == 'yes') ? 1 : 0,
                                    'shipping_type' => ($mainVariant->shipping_type == '') ? 'Standard Shipping' : $mainVariant->shipping_type,
                                    'source_id' => $mainVariant->selected_options,
                                    'source_price' => ($mainVariant->amazon_price != '') ? (float)str_replace(' ', '', $mainVariant->amazon_price) : 0.00,
                                    'shipping_price' => ($mainVariant->shipping_price != '') ? (float)str_replace(' ', '',$mainVariant->shipping_price) : 0.00,
                                    'shopify_price' => ($mainVariant->price != '') ? (float)str_replace(' ', '',$mainVariant->price) : 0.00,
                                    'created_at' => $mainVariant->created_at,
                                    'updated_at' => date("Y-m-d H:i:s")
                                ];
//                                \Log::info('============== main Amazon ==========');
//                                \Log::info($variantRecs);
                                $variantID = DB::table('variants')->insertGetId($variantRecs);

                                //add attribute
                                $keys = json_decode($mainVariant->var_keys);
                                $attributes = (array) json_decode($mainVariant->options);
                                $sku = $mainVariant->selected_options;

                                $attr = (@$attributes[$sku]) ? @$attributes[$sku] : '';

                                if ($attr != '') {
                                    foreach ($keys as $vk => $vv) {
                                        DB::table('attributes')->insert([
                                            'variant_id' => $variantID, 'dimension' => $vv, 'value' => $attr[$vk]
                                        ]);
                                    }
                                }

                                // add other variants

                                $lvvariants = $live_db->select("SELECT * FROM products_meta WHERE store_id = ".$val->store_id." AND parent_id =
                        ".$mainVariant->id);

                                if (!empty($lvvariants)) {
                                    foreach ($lvvariants as $lvkey => $lvval) {
                                        $vdtails = json_decode($val->variants_details);
                                        if( !empty( $vdtails ) ){
                                            foreach ( $vdtails as $vk=>$vv ){
                                                $url = '';
                                                if( $vv->sku ==  $lvval->selected_options){
                                                    $url = $vv->variant_img;
                                                    logger($url);
                                                    break;
                                                }
                                            }
                                        }
                                        // add image
                                        $imageRec = [
                                            'product_id' => $lcl_product_id,
                                            'url' => $url,
                                            'created_at' => date("Y-m-d H:i:s"),
                                            'updated_at' => date("Y-m-d H:i:s")
                                        ];
                                        $imageID = DB::table('images')->insertGetId($imageRec);
                                       // logger($imageID);
                                        // get inventory
                                        $inventory = $this->getInventory($sh_product, $lvval->selected_options);

                                        // add variant
                                        $variantRecs = [
                                            'product_id' => $lcl_product_id,
                                            'image_id' => $imageID,
                                            'is_main' => false,
                                            'title' => $sh_title,
                                            'in_stock' => 0,
                                            'inventory' => $inventory,
                                            'prime_eligible' => ($lvval->is_prime == 'yes') ? 1 : 0,
                                            'shipping_type' => ($lvval->shipping_type == '') ? 'Standard Shipping' : $lvval->shipping_type,
                                            'source_id' => $lvval->selected_options,
                                            'source_price' => ($lvval->amazon_price != '') ? (float)str_replace(' ', '', $lvval->amazon_price) : 0.00,
                                            'shipping_price' => ($lvval->shipping_price != '') ? (float)str_replace(' ', '',$lvval->shipping_price) : 0.00,
                                            'shopify_price' => ($lvval->price != '') ? (float)str_replace(' ', '',$lvval->price) : 0.00,
                                            'created_at' => $lvval->created_at,
                                            'updated_at' => date("Y-m-d H:i:s")
                                        ];
//                                        \Log::info('============== Sub Amazon ==========');
//                                        \Log::info($variantRecs);
                                        $variantID = DB::table('variants')->insertGetId($variantRecs);

                                        //add attribute
                                        $sku = $lvval->selected_options;

                                        $attr = (@$attributes[$sku]) ? @$attributes[$sku] : '';

                                        if ($attr != '') {
                                            foreach ($keys as $vk => $vv) {
                                                DB::table('attributes')->insert([
                                                    'variant_id' => $variantID, 'dimension' => $vv,
                                                    'value' => $attr[$vk]
                                                ]);
                                            }
                                        }
                                    }
                                }
                            }else{

                                $aff_btn_txt = 'Buy from Amazon';
                                $affiliate = [
                                    'country' => $locale,
                                    'status' => false,
                                    'button_style' => 0,
                                    'button_text' => $aff_btn_txt
                                ];
                                // add product
                                $productRec = [
                                    'user_id' => $this->user_store_id,
                                    'shopify_id' => $val->shopify_prod_id,
                                    'shopify_handle' => (@$sh_handle) ? $sh_handle : '',
                                    'collection_id' => 0,
                                    'type' => '',
                                    'locale' => $locale,
                                    'source' => ucwords($source),
                                    'source_url' => $val->prod_url,
                                    'description' => '',
                                    'affiliate' => json_encode($affiliate),
                                    'saller_ranks' => '',
                                    'created_at' => $val->created_at,
                                    'updated_at' => $val->updated_at,
                                    'is_migrated' => 1
                                ];
                                $lcl_product_id = $new_db->table('products')->insertGetId($productRec);

                                // add main image
                                $imageRec = [
                                    'product_id' => (@$lcl_product_id) ? $lcl_product_id : '',
                                    'url' => (@$no_vrnt_image) ? $no_vrnt_image : '',
                                    'created_at' => date("Y-m-d H:i:s"),
                                    'updated_at' => date("Y-m-d H:i:s")
                                ];
                                $imageID = DB::table('images')->insertGetId($imageRec);

                                $inventory = $this->getInventory($sh_product, '');
                                // add main variant
                                $variantRecs = [
                                    'product_id' => $lcl_product_id,
                                    'image_id' => $imageID,
                                    'is_main' => true,
                                    'title' => $sh_title,
                                    'in_stock' => 0,
                                    'inventory' => $inventory,
                                    'prime_eligible' => 0,
                                    'shipping_type' => ($val->shipping_type == '') ? 'Standard Shipping' : $val->shipping_type,
                                    'source_id' => '',
                                    'source_price' => ($val->amazon_price != '') ? (float)str_replace(' ', '', $val->amazon_price) : 0.00,
                                    'shipping_price' => ($val->shipping_price != '') ? (float)str_replace(' ', '',$val->shipping_price) : 0.00,
                                    'shopify_price' => ($val->price_prod != '') ? (float)str_replace(' ', '',$val->price_prod) : 0.00,
                                    'created_at' => $val->created_at,
                                    'updated_at' => $val->updated_at
                                ];

                                $variantID = DB::table('variants')->insertGetId($variantRecs);
                            }

                        } else {
                            if ($source == 'walmart') {
                                $walmart_meta_query = $live_db->select("SELECT * FROM walmart_products_meta WHERE store_id = ".$val->store_id."  AND product_url = '".$val->prod_url."'");

                                $lvproMeta = $walmart_meta_query;
                                $locale = 'US';
//                        \Log::info($lvproMeta);
                                $sh_product = $this->getShopifyProduct($val->store_id, $live_db);
                                $sh_handle = '';
                                $sh_title = '';
                                if ($sh_product != false) {
                                    $sh_handle = $sh_product->handle;
                                    $sh_title = $sh_product->title;
                                    $no_vrnt_image = $sh_product->image['src'];
                                }else{
                                    $no_vrnt_image = '';
                                }
                                $affiliate = [
                                    'country' => 'WWW.WALMART.COM',
                                    'status' => false,
                                    'button_style' => 0,
                                    'button_text' => 'Buy from Amazon'
                                ];
                                if (!empty($lvproMeta)) {
                                    $mainVariant = $lvproMeta[0];

                                    // add product
                                    $productRec = [
                                        'user_id' => $this->user_store_id,
                                        'shopify_id' => $val->shopify_prod_id,
                                        'shopify_handle' => $sh_handle,
                                        'collection_id' => 0,
                                        'type' => '',
                                        'locale' => $locale,
                                        'source' => ucwords($source),
                                        'source_url' => $val->prod_url,
                                        'description' => $mainVariant->description,
                                        'affiliate' => json_encode($affiliate),
                                        'saller_ranks' => '',
                                        'created_at' => $val->created_at,
                                        'updated_at' => $val->updated_at,
                                        'is_migrated' => 1
                                    ];
                                    $lcl_product_id = $new_db->table('products')->insertGetId($productRec);

                                    // add main image
                                    $imageRec = [
                                        'product_id' => $lcl_product_id,
                                        'url' => $mainVariant->featured_image,
                                        'created_at' => date("Y-m-d H:i:s"),
                                        'updated_at' => date("Y-m-d H:i:s")
                                    ];
                                    $imageID = DB::table('images')->insertGetId($imageRec);

                                    $inventory = $this->getInventory($sh_product, $mainVariant->item_id);
                                    // add main variant
                                    $variantRecs = [
                                        'product_id' => $lcl_product_id,
                                        'image_id' => $imageID,
                                        'is_main' => true,
                                        'title' => $sh_title,
                                        'in_stock' => 0,
                                        'inventory' => $inventory,
                                        'prime_eligible' => 0,
                                        'shipping_type' => ($val->shipping_type == '') ? 'Standard Shipping' : $val->shipping_type,
                                        'source_id' => $mainVariant->item_id,
                                        'source_price' => ($val->amazon_price != '') ? (float)str_replace(' ', '', $val->amazon_price) : 0.00,
                                        'shipping_price' => ($val->shipping_price != '') ? (float)str_replace(' ', '',$val->shipping_price) : 0.00,
                                        'shopify_price' => ($val->price_prod != '') ? (float)str_replace(' ', '',$val->price_prod) : 0.00,
                                        'created_at' => $mainVariant->created_at,
                                        'updated_at' => date("Y-m-d H:i:s")
                                    ];

                                    $variantID = DB::table('variants')->insertGetId($variantRecs);

                                    //add attribute
                                    $keys = json_decode($mainVariant->var_keys);
                                    $attributes = (array) json_decode($mainVariant->variants);
                                    $sku = $mainVariant->item_id;

                                    $attr = (@$attributes[$sku]) ? @$attributes[$sku] : '';

                                    if ($attr != '') {
                                        foreach ($keys as $vk => $vv) {
                                            DB::table('attributes')->insert([
                                                'variant_id' => $variantID, 'dimension' => $vv, 'value' => $attr[$vk]
                                            ]);
                                        }
                                    }

                                    // add other variants

                                    if ($mainVariant->var_data != '') {
                                        $lvvariants = json_decode($mainVariant->var_data);

                                        if (!empty($lvvariants)) {
                                            foreach ($lvvariants as $lvkey => $lvval) {
                                                // add image
                                                $imageRec = [
                                                    'product_id' => $lcl_product_id,
                                                    'url' => $lvval->largeImage,
                                                    'created_at' => date("Y-m-d H:i:s"),
                                                    'updated_at' => date("Y-m-d H:i:s")
                                                ];
                                                $imageID = DB::table('images')->insertGetId($imageRec);

                                                // get inventory
                                                $inventory = $this->getInventory($sh_product, $lvval->itemId);

                                                // add variant
                                                $variantRecs = [
                                                    'product_id' => $lcl_product_id,
                                                    'image_id' => $imageID,
                                                    'is_main' => false,
                                                    'title' => $sh_title,
                                                    'in_stock' => 0,
                                                    'inventory' => $inventory,
                                                    'prime_eligible' => 0,
                                                    'shipping_type' => ($val->shipping_type == '') ? 'Standard Shipping' : $val->shipping_type,
                                                    'source_id' => $lvval->itemId,
                                                    'source_price' => ($lvval->salePrice != '') ? (float)(str_replace(' ', '',$lvval->salePrice)) : 0.00,
                                                    'source_price' => ($lvval->salePrice != '') ? (float)str_replace(' ', '',$lvval->salePrice) : 0.00,
                                                    'shipping_price' => ($val->shipping_price != '') ? (float)str_replace(' ', '',$val->shipping_price) : 0.00,
                                                    'shopify_price' => ($lvval->salePrice != '') ? (float)str_replace(' ', '',$lvval->salePrice) : 0.00,
                                                    'created_at' => $mainVariant->created_at,
                                                    'updated_at' => date("Y-m-d H:i:s")
                                                ];
                                                $variantID = DB::table('variants')->insertGetId($variantRecs);

                                                //add attribute
                                                $sku = $lvval->itemId;

                                                $attr = (@$attributes[$sku]) ? @$attributes[$sku] : '';

//                                                \Log::info(json_encode($attr));
//                                                \Log::info(json_encode($keys));
                                                if ($attr != '') {
                                                    foreach ($keys as $vk => $vv) {
                                                        if (@$attr[$vk]) {
                                                            DB::table('attributes')->insert([
                                                                'variant_id' => $variantID, 'dimension' => $vv,
                                                                'value' => $attr[$vk]
                                                            ]);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }else{
                                    // add product
                                    $productRec = [
                                        'user_id' => $this->user_store_id,
                                        'shopify_id' => $val->shopify_prod_id,
                                        'shopify_handle' => $sh_handle,
                                        'collection_id' => 0,
                                        'type' => '',
                                        'locale' => $locale,
                                        'source' => ucwords($source),
                                        'source_url' => $val->prod_url,
                                        'description' => '',
                                        'affiliate' => json_encode($affiliate),
                                        'saller_ranks' => '',
                                        'created_at' => $val->created_at,
                                        'updated_at' => $val->updated_at,
                                        'is_migrated' => 1
                                    ];
                                    $lcl_product_id = $new_db->table('products')->insertGetId($productRec);

                                    // add main image
                                    $imageRec = [
                                        'product_id' => (@$lcl_product_id) ? $lcl_product_id : '',
                                        'url' => (@$no_vrnt_image) ? $no_vrnt_image : '',
                                        'created_at' => date("Y-m-d H:i:s"),
                                        'updated_at' => date("Y-m-d H:i:s")
                                    ];
                                    $imageID = DB::table('images')->insertGetId($imageRec);

                                    $inventory = $this->getInventory($sh_product, '');
                                    // add main variant
                                    $variantRecs = [
                                        'product_id' => $lcl_product_id,
                                        'image_id' => $imageID,
                                        'is_main' => true,
                                        'title' => $sh_title,
                                        'in_stock' => 0,
                                        'inventory' => $inventory,
                                        'prime_eligible' => 0,
                                        'shipping_type' => ($val->shipping_type == '') ? 'Standard Shipping' : $val->shipping_type,
                                        'source_id' => '',
                                        'source_price' => ($val->amazon_price != '') ? (float)str_replace(' ', '', $val->amazon_price) : 0.00,
                                        'shipping_price' => ($val->shipping_price != '') ? (float)str_replace(' ', '',$val->shipping_price) : 0.00,
                                        'shopify_price' => ($val->price_prod != '') ? (float)str_replace(' ', '',$val->price_prod) : 0.00,
                                        'created_at' => $val->created_at,
                                        'updated_at' => $val->updated_at
                                    ];

                                    $variantID = DB::table('variants')->insertGetId($variantRecs);
                                }
                            }
                        }
                    }
                }
            }

            \Log::info('================= END:: Product Migration ===============');
        } catch (\Exception $e) {
            \Log::info($e);
        }
    }

    public function getLocale($url)
    {
        $split = explode('.', $url);

        $str = (@$split[3]) ? $split[3] : $split[2];
        $domain = strtoupper(substr($str, 0, strpos($str, '/')));
        $domain = ($domain === 'COM') ? 'US' : $domain;
        $domain = ($domain === 'UK') ? 'GB' : $domain;
        return $domain;
    }

    public function getShopifyProduct($store_id, $live_db)
    {
        try {
            $user = $live_db->select("SELECT * FROM usersettings WHERE id = ".$store_id);
            $user = $user[0];
            $parameter['fields'] = 'id,handle,title,variants,image';
            $api = new Shopify($user->store_name, $user->access_token);
            $result = $api->call('GET', 'admin/products/5207463985290.json', $parameter);

            if (@$result->product) {
                return $result->product;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getInventory($sh_product, $sku)
    {
        $inventory = 0;
        if (!empty($sh_product->variants)) {
            $sh_variant = $sh_product->variants;
            foreach ($sh_variant as $vkey => $vval) {
                if ($vval->sku == $sku) {
                    $inventory = $vval->inventory_quantity;
                }
            }
        }
        return $inventory;
    }

    public function counters($live_db, $new_db)
    {
        try {

            \Log::info('================= START:: Counter Migration ===============');
            $store_id = $this->store_id;
            $user_settings = $live_db->select("SELECT * FROM usersettings WHERE id = " . $store_id);
            foreach ($user_settings as $key => $val) {
                \Log::info($val->id);
                $charge_id = '';
                if( $val->plan == 1 ){

                    $new_db->update("UPDATE users SET plan_id = null WHERE id = ".$val->id);
                }else{
                    $charges = $new_db->select("SELECT * FROM charges WHERE user_id = ".$val->id);
                    if (!empty($charges)) {
                        $charge = $charges[0];
                        $charge_id = $charge->id;
                        $start_date = $charge->activated_on;
                        $end_date = date('Y-m-d H:i:s', strtotime($charge->activated_on.' + 30 days'));
                    }
                }

                $subscription_date = date('Y-m-d', strtotime($val->current_plan_date));
                $arr = explode('-', $subscription_date);
                $this_month = date('m');
                $this_year = date('Y');
                $this_date = $arr[2];

                if ($this_date > date('d')) {
                    $end_date = $this_year.'-'.$this_month.'-'.($this_date - 1);
                    $start_date = date('Y-m-d', strtotime('-1 months', strtotime($end_date.' +1 day')));
                } else {
                    $start_date = $this_year.'-'.$this_month.'-'.$this_date;
                    $end_date = date('Y-m-d', strtotime('+1 months', strtotime($start_date.' -1 day')));
                }

                $product_cnt = $live_db->select("SELECT count(*) as product_cnt FROM products WHERE status=1 AND store_id = $val->id AND created_at BETWEEN '$start_date' AND '$end_date' AND prod_aff_url = ''");
                $product_cnt = $product_cnt[0];

                $affiliate_cnt = $live_db->select("SELECT count(*) as affiliate_cnt FROM products WHERE status=1 AND store_id = $val->id AND created_at BETWEEN '$start_date' AND '$end_date' AND prod_aff_url != ''");
                $affiliate_cnt = $affiliate_cnt[0];

                $regular_variant_cnt = $live_db->select("SELECT SUM(import_variant_cnt) as regular_variant_cnt FROM products WHERE status=1 AND store_id = $val->id AND prod_aff_url = '' AND created_at BETWEEN  '$start_date' AND '$end_date'");
                $regular_variant_cnt = $regular_variant_cnt[0];

                $affiliate_variant_cnt = $live_db->select("SELECT SUM(import_variant_cnt) as affiliate_variant_cnt FROM products WHERE status=1 AND store_id = $val->id AND prod_aff_url != '' AND created_at BETWEEN  '$start_date' AND '$end_date'");
                $affiliate_variant_cnt = $affiliate_variant_cnt[0];

                $counter['user_id'] = $this->user_store_id;
                $counter['charge_id'] = $charge_id;
                $counter['plan_id'] = $val->plan;
                $counter['regular_product_count'] = (@$product_cnt->product_cnt) ? $product_cnt->product_cnt : 0;
                $counter['regular_product_variant_count'] = (@$regular_variant_cnt->regular_variant_cnt) ? $regular_variant_cnt->regular_variant_cnt : 0;
                $counter['affiliate_product_count'] = (@$affiliate_cnt->affiliate_cnt) ? $affiliate_cnt->affiliate_cnt : 0;
                $counter['affiliate_product_variant_count'] = (@$affiliate_variant_cnt->affiliate_variant_cnt) ? $affiliate_variant_cnt->affiliate_variant_cnt : 0;
                $counter['status'] = 'active';
                $counter['is_disable_freemium'] = ($val->plan == 1) ? 0 : 1;
                $counter['start_date'] = $start_date;
                $counter['end_date'] = $end_date;
                $new_db->table('counters')->insert($counter);
            }


            \Log::info('================= END:: Counter Migration ===============');
        } catch (\Exception $e) {
            \Log::info($e);
        }
    }

    public function affiliates($live_db, $new_db){
        try {
            \Log::info('================= START:: affiliates Migration ===============');
            $store_id = $this->store_id;
            $affiliates = $live_db->select('SELECT * FROM affiliates WHERE store_id = ' . $store_id);
            foreach ( $affiliates as $key=>$val ){
                $aff['id'] = $val->id;
                $aff['user_id'] = $this->user_store_id;
                $aff['locale'] = strtoupper($val->country_code);
                $aff['associate_id'] = $val->associate_id;
                $new_db->table('amazon_associates')->insert($aff);
            }
            \Log::info('================= END:: affiliates Migration ===============');
        } catch (\Exception $e) {
            \Log::info($e);
        }
    }

    public function ActiveFreePlan($live_db, $store_id, $new_db){
        try{
            logger('ActiveFreePlan');
            $charge_id = '';
            $plan = $new_db->select("SELECT * FROM plans WHERE id = 1");
            $plan = $plan[0];
//            create charge
            $user = $live_db->select("SELECT * FROM usersettings WHERE id = ".$store_id);
            $user = $user[0];
            $api = new Shopify($user->store_name, $user->access_token);

            $charge = [
                "application_charge"=> [
                    "name"=> $plan->name,
                    "price"=> 0.5,
                    "return_url"=> env('APP_URL'),
                    "capped_amount"=> $plan->capped_amount,
                    "terms"=> $plan->terms,
                    "test" => $plan->test,
                ]
            ];
            $endPoint = '/admin/api/'.env('SHOPIFY_API_VERSION').'/application_charges.json';
            logger($endPoint);
            $result = $api->call('POST', $endPoint, $charge);
          //  logger(json_encode($result));
            if( @$result->application_charge ){
                $charge_data = $result->application_charge;

                $data['user_id'] = $this->user_store_id;
                $data['charge_id'] = $charge_data->id;
                $data['test'] = $charge_data->test;
                $data['status'] = 'ACTIVE';
                $data['name'] = $charge_data->name;
                $data['type'] = 'ONETIME';
                $data['price'] = $charge_data->price;
                $data['trial_days'] = 0;
                $data['billing_on'] = date("Y-m-d H:i:s", strtotime($charge_data->created_at));
                $data['activated_on'] = date("Y-m-d H:i:s", strtotime($charge_data->created_at));
                $data['created_at'] = date("Y-m-d H:i:s", strtotime($charge_data->created_at));
                $data['updated_at'] = date("Y-m-d H:i:s", strtotime($charge_data->updated_at));
                $data['plan_id'] = 1;
                $new_db->table('charges')->insert($data);

                $new_db->update("UPDATE users SET plan_id = 1 WHERE id = " . $store_id);
            }else{
             //   logger(json_encode($result));
            }
            return $charge_id;
        }catch( \Exception $e ){
            logger('ERROR :: ActiveFreePlan');
            //logger(json_encode($e));
        }
    }

    public function ScriptTag(){
        try{
           // logger('================= START:: ScriptTag =================');
            // $id = 63901;
            $users = User::where('id', $this->user_store_id)->get();
            // $users = User::get();
            $checkScript = 'https://amazonedropshipping.com/js/amazon-dropshipper.js';
            foreach ( $users as $ukey=>$uval ){
                $exist_shop = $this->getShop($uval);
                if( !$exist_shop ){

                    $sh_scripts = $this->getScripts($uval);
                    if( !empty($sh_scripts) ){
                        $res = array_search($checkScript, array_column($sh_scripts, 'src'));
                        if($res == ''){
                            $this->createScript($uval, $checkScript);
                        }
                    }
                }
            }
          //  logger('================= END:: ScriptTag =================');
        }catch( \Exception $e ){
            logger('================= ERROR:: ScriptTag =================');
          //  logger(json_encode($e));
        }
    }

    public function createScript($user, $url){
        try{
           // logger('================= START:: createScript =================');

            $endPoint = 'admin/api/' . env('SHOPIFY_API_VERSION') . '/script_tags.json';
            $script = [
                'script_tag' => [
                    "event"=> "onload",
                    "src"=> $url
                ]
            ];
            $result = $user->api()->rest('POST', $endPoint, $script);

           // logger(json_encode($result));
           // logger('================= END:: createScript =================');
        }catch( \Exception $e ){
            logger('================= ERROR:: createScript =================');
          //  logger(json_encode($e));
        }
    }

    public function getScripts($user){
        try{
           // logger('================= START:: getScripts =================');
            $endPoint = 'admin/api/' . env('SHOPIFY_API_VERSION') . '/script_tags.json';
            $parameter['fields'] = 'id,src';
            $result = $user->api()->rest('GET', $endPoint, $parameter);

            $script_tags = [];
            if( !$result->errors ){
                $script_tags = $result->body->script_tags;
            }
            return $script_tags;
            logger('================= END:: getScripts =================');
        }catch( \Exception $e ){
            logger('================= ERROR:: getScripts =================');
           // logger(json_encode($e));
        }
    }

    public function getShop($user){
        try{
          //  logger('================= START:: getShop =================');
            $endPoint = 'admin/api/' . env('SHOPIFY_API_VERSION') . '/shop.json';
            $result = $user->api()->rest('GET', $endPoint);
            return $result->errors;
           // logger('================= END:: getShop =================');
        }catch( \Exception $e ){
            logger('================= ERROR:: getShop =================');
           // logger(json_encode($e));
        }
    }

    public function Webhook(){
        try{
           // logger('================= START:: Webhook =================');
            $users = User::where('id', $this->user_store_id)->get();

            $newURL =  ['products/update' => 'https://amazonedropshipping.com/webhook/products-update', 'products/delete' => 'https://amazonedropshipping.com/webhook/products-delete', 'app/uninstalled' => 'https://amazonedropshipping.com/webhook/app-uninstalled'];

            foreach ( $users as $ukey=>$uval ){
                $exist_shop = $this->getShop($uval);
                if( !$exist_shop ){
                    $oldURLs = ['https://amazonedropshipping.com/shopify_app/webhooks_handle/hook_product_update.php?storeid=' . $uval->id, 'https://amazonedropshipping.com/shopify_app/webhooks_handle/hook_product_delete.php?storeid=' . $uval->id, 'https://amazonedropshipping.com/shopify_app/webhooks_handle/hook_app_uninstall.php?storeid=' . $uval->id];
                    // $oldURLs = ['https://amazonedropshipping.com/shopify_app/webhooks_handle/hook_product_update.php?storeid=' . $this->store_id, 'https://amazonedropshipping.com/shopify_app/webhooks_handle/hook_product_delete.php?storeid=' . $uval->id, 'https://amazonedropshipping.com/shopify_app/webhooks_handle/hook_app_uninstall.php?storeid=' . $this->store_id];

                    $sh_webhooks = $this->getWebhooks($uval);
                    if( !empty($sh_webhooks)){
                        $address = [];
                        foreach ($sh_webhooks as $wkey => $wvalue) {
                            $address[$wvalue->id] = $wvalue->address;
                        }
                        foreach ($oldURLs as $key => $value) {
                            if( in_array($value, $address) ){
                                $id = array_search($value, $address);
                                $this->deleteWebhook($uval, $id);
                            }
                        }

                        foreach ($newURL as $key => $value) {
                            if( !in_array($value, $address) ){
                                $this->createWebhook($uval, $key, $value);
                            }
                        }

                        $sh_webhooks = $this->getWebhooks($uval);
                       // logger($sh_webhooks);
                    }
                }
            }
           // logger('================= END:: Webhook =================');
        }catch( \Exception $e ){
            logger('================= ERROR:: Webhook =================');
            logger($e);
        }
    }

    public function getWebhooks($user){
        try{
          //  logger('================= START:: getWebhooks =================');
            $endPoint = 'admin/api/' . env('SHOPIFY_API_VERSION') . '/webhooks.json';
            $parameter['fields'] = 'id,address,topic';
            $result = $user->api()->rest('GET', $endPoint, $parameter);

            $webhooks = [];
            if( !$result->errors ){
                $webhooks = $result->body->webhooks;
            }
            return $webhooks;
            logger('================= END:: getWebhooks =================');
        }catch( \Exception $e ){
            logger('================= ERROR:: getWebhooks =================');
           // logger(json_encode($e));
        }
    }

    public function deleteWebhook($user, $webhook_id){
        try{
           // logger('================= START:: deleteWebhook =================');
          //  logger($webhook_id);
            $endPoint = 'admin/api/' . env('SHOPIFY_API_VERSION') . '/webhooks/'. $webhook_id .'.json';
            $result = $user->api()->rest('DELETE', $endPoint);

            // logger(json_encode($result));
           // logger('================= END:: deleteWebhook =================');
        }catch( \Exception $e ){
           // logger('================= ERROR:: deleteWebhook =================');
            logger($e);
        }
    }

    public function updateWebhook($user, $webhook_id, $address){
        try{
          //  logger('================= START:: updateWebhook =================');
            $endPoint = 'admin/api/' . env('SHOPIFY_API_VERSION') . '/webhooks/'. $webhook_id .'.json';
            //logger($address);
            $webhook = [
                'webhook' => [
                    "id"=> $webhook_id,
                    "address"=> $address
                ]
            ];
            $result = $user->api()->rest('PUT', $endPoint, $webhook);

            // logger(json_encode($result));
            logger('================= END:: updateWebhook =================');
        }catch( \Exception $e ){
            logger('================= ERROR:: updateWebhook =================');
            logger($e);
        }
    }

    public function createWebhook($user, $topic, $address){
        try{
           // logger('================= START:: createWebhook =================');
            $endPoint = 'admin/api/' . env('SHOPIFY_API_VERSION') . '/webhooks.json';
            $webhook = [
                'webhook' => [
                    "topic"=> $topic,
                    "address"=> $address,
                    "format" => "json"
                ]
            ];

            $result = $user->api()->rest('POST', $endPoint, $webhook);

            //logger(json_encode($result));
            logger('================= END:: createWebhook =================');
        }catch( \Exception $e ){
            logger('================= ERROR:: createWebhook =================');
            logger($e);
        }
    }

}
