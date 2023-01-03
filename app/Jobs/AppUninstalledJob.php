<?php

namespace App\Jobs;


use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Osiset\ShopifyApp\Actions\CancelCurrentPlan;
use Osiset\ShopifyApp\Contracts\Commands\Shop as IShopCommand;
use Osiset\ShopifyApp\Contracts\Objects\Values\ShopDomain;
use Osiset\ShopifyApp\Contracts\Queries\Shop as IShopQuery;
use App\Mail\ReceivesAppUninstalledMail;
use DB;
use Carbon\Carbon;
class AppUninstalledJob extends \Osiset\ShopifyApp\Messaging\Jobs\AppUninstalledJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Shop's myshopify domain
     *
     * @var ShopDomain
     */
    public $shopDomain;

    /**
     * The webhook data
     *
     * @var object
     */
    public $data;

    /**
     * Create a new job instance.
     *
     * @param string   $shopDomain The shop's myshopify domain
     * @param stdClass $data    The webhook data (JSON decoded)
     *
     * @return void
     */
    public function __construct($shopDomain, $data)
    {
        $this->shopDomain = $shopDomain;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        IShopCommand $shopCommand,
        IShopQuery $shopQuery,
        CancelCurrentPlan $cancelCurrentPlanAction
    ): bool {
        logger('=============== AppUninstalledJob =============');
//        $domain = $this->shopDomain->toNative();
       // logger(json_encode($this->shopDomain));
        // Get the shop
        $user = $shopQuery->getByDomain($this->shopDomain);

if($user){

        logger(json_encode($user));
        $shopId = $user->getId();

   // $this->deleteRegisterWebhook($shopId);


        // Cancel the current plan
        $cancelCurrentPlanAction($shopId);

        // Purge shop of token, plan, etc.
//        $shopCommand->clean($shopId);

       // logger("Plans ID::".$user->plan_id);


//        if($user->plan_id!==1){
//
//            $plans = DB::table('plans')->where('id',$user->plan_id)->first();
//            logger(json_encode($plans));
//
//            if($plans) {
//
//                // $to_mail = "satish.crawlapps@gmail.com";
//
//                    $to_mail = $user->email;
//
//                      logger("Email recevier :: ".$to_mail);
//
//                if (!empty($to_mail)) {
//
//                    $appUnInstall_date = Carbon::now()->format('Y-m-d');
//
//                    $charges = DB::table('charges')->select('created_at')->where('user_id',$user->id)->where('plan_id',$user->plan_id)->where('status',"ACTIVE")->first();
//
//                    if($charges) {
//
//                        $firstDayOfPaidPlan = Carbon::parse($charges->created_at)->format('d');
//
//                        $params = [
//                            "user_plan" => $plans->name,
//                            "app_uninstall_date" => $appUnInstall_date,
//                            "first_day_of_paid_plan" => $firstDayOfPaidPlan
//                        ];
//
//                        logger("un-install email param");
//                        logger(json_encode($params));
//
//                        Mail::to($to_mail)->send(new ReceivesAppUninstalledMail($params));
//
//                    }
//                }
//
//
//            }
//        }

        $user->password = '';
        $user->plan_id = null;
        $user->save();



        // Soft delete the shop.
        $shopCommand->softDelete($shopId);



}

        return true;
    }



    public function deleteRegisterWebhook($user_id){
        try{
            logger("===============START :: webhooks :: DELETE =================");
            logger("user_id ".$user_id);

            $user = User::where('id', $user_id)->first();
            if($user){

                $webhooks = webhooksList($id);

                    if(count($webhooks) > 0){

                       foreach($webhooks as $webhook){

                           $webhook_id = $webhook->id;

                           //START :: DELETE WEBHOOK

                           $webhookDeleteReq = $shop->api()->rest('DELETE', '/admin/api/'.env('SHOPIFY_API_VERSION').'/webhooks/'.$webhook_id.'.json');

                           logger("===============START :: webhooks :: DELETE Result=================");
                           logger(json_encode($webhookDeleteReq));
                           logger('================= END::  webhooks :: DELETE =================');

                           //END :: DELETE WEBHOOK

                       }

                    }

            }else{
                logger("user not found!");
            }

            logger('================= END:: webhooks :: DELETE =================');
        }catch( \Exception $e ){
            logger('================= ERROR:: webhooks :: DELETE =================');
            logger($e->getMessage());
            return true;
        }
    }


}
