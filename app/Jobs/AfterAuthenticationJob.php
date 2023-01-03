<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\ShopifyShop;
use App\User;
class AfterAuthenticationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $shopAuthUser;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($shopAuthUser)
    {
        //logger("-----------------------------------");
       // logger("AfterAuthenticateJob Job is start!");
        //logger("-----------------------------------");
       // logger("shopAuthUser objct:".json_encode($shopAuthUser));
        $this->shopAuthUser = $shopAuthUser;
       // logger("name :".$shopAuthUser['name']);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

       // logger("AfterAuthenticateJob Job is handle!");

        try {

          //  logger("shop :: ".$this->shopAuthUser['name']);

            $shop = User::where('name', $this->shopAuthUser['name'])->firstOrFail();

            $setting = DB::table('settings')->insertOrIgnore([ 'user_id' => $shop->id]);
            \Log::info(json_encode($setting));


           // logger("Shop user:".json_encode($shop));

            if ($shop) {

               // logger("Shop's API objct:".json_encode($shop));

                $user_id = $shop['id'];

                $exist_shop = ShopifyShop::where('user_id',$user_id)->first();

                logger("shop");
                logger(json_encode($exist_shop));

                if(!$exist_shop) {

                    $shopApi = $shop->api()->rest('GET', '/admin/shop.json');

                     logger("shopAPI");
                     logger(json_encode($shopApi));

                    if (!$shopApi->errors) {

                        logger("shopAPI");
                  
                         $shopApi = (array)$shopApi->body->shop;
                               logger(json_encode($shopApi));

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
                       // logger("-----------------------------------");
                    } else {
                      //  logger("error during call Shop API !");
                    }
                }else{
                   // logger("Shop Exist!");
                }

            } else {
               // logger("-----------------------------------");
               // logger("Shop Not Found!");
               // logger("-----------------------------------");
            }


        } catch (Exception $e) {
            logger("AfterAuthenticateJob Job is Exception!");
            logger('Message: '.$e->getMessage());
        }
       // logger("-----------------------------------");
       // logger("AfterAuthenticateJob Job is end!");
      //  logger("-----------------------------------");



    }
}
