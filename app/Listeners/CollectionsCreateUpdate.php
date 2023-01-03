<?php

namespace App\Listeners;

use App\Events\CheckCollections;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\User;
use Illuminate\Support\Facades\DB;
class CollectionsCreateUpdate
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  CheckCollections  $event
     * @return void
     */
    public function handle(CheckCollections $event)
    {
        try {
            //logger('========== Listener:: Collections create Update ==========');
            $ids = $event->ids;
            $user_id = $ids['user_id'];
            $data = $ids['data'];

            //logger("Webhook :: Collections :: DATA");
           // logger(json_encode($data));


                 $shop = User::where('id', $user_id)->first();

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

                                   //    logger("is array");
                                       if (!in_array($val->collection_id, $row)) {
                                           array_push($row, $val->collection_id);
                                       }

                                       $collection_ids = $row;

                                   } else {
                                     //  logger("is string semicoprated");

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


        }catch ( \Exception $e ){
            logger('========== ERROR:: Listener:: Collections create Update ==========');
            logger($e);
        }

    }
}
