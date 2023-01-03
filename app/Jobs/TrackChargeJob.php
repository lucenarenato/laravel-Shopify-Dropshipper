<?php

namespace App\Jobs;

use App\Models\Counters;
use App\Traits\GraphQLTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Osiset\ShopifyApp\Services\ChargeHelper;
use Osiset\ShopifyApp\Objects\Values\ChargeReference;
use App\User;
use Osiset\ShopifyApp\Storage\Models\Charge;
use Illuminate\Support\Facades\DB;
use DateTime;
class TrackChargeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use GraphQLTrait;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            logger('========== START:: TrackChargeJob =========');
            // Setup the "Charge Helper"
            $chs = resolve(ChargeHelper::class);
            $shops = User::all();

            foreach ($shops as $shop) {

                logger('SHOP :: ' . $shop->name);
                if( $shop->password ){

                    $plan = $shop->plan;

                    if ($plan === null || $plan->id == 1) {
                        // No plan/Free plan, skip
                        continue;
                    }

                    $charge_shop = Charge::where('plan_id', $plan->id)->where('user_id', $shop->id)->where('status', 'ACTIVE')->first();


                    if($charge_shop) {

                        $billing_on = $charge_shop->billing_on;

                        logger("billing_on =>".$billing_on);

                            $current_date = date('Y-m-d H:i:s');

                            logger("current_date =>".$current_date);

                            $date1 = new DateTime($billing_on);
                            $date2 = new DateTime($current_date);

                            $interval = $date1->diff($date2);

                           logger("date1 =>".json_encode($date1));
                            logger("date2 =>".json_encode($date2));
                             logger("interval =>".json_encode($interval));


                              $next_billing_for = $interval->days;

                              logger("next_billing_for =>".json_encode($next_billing_for));

                               if($date1 >= $date2 && $next_billing_for <= 5){

                                   logger("Upcoming billing is after ".$next_billing_for." days.");


                    $exist_shop = $this->getShop($shop);


                    //logger('================= $exist_shop ::  ===============');
                   // logger( $exist_shop );
                    if( !$exist_shop ){
                        logger('================= shop exist ===============');

                        // Get the charge entry from database, set it to the charge helper to use for API calls
                        $charge = $chs->chargeForPlan($plan->getId(), $shop);



                        if( $charge ){
                            logger('================= charge exist ===============');
                           // logger(json_encode($charge));


                            $chs->useCharge($charge->getReference());



                            // Get the charge data from Shopify API
                            $chargeData = $chs->retrieve($shop);

                           logger(json_encode($chargeData));

                            if( $chargeData->status == 'active'){
                                $curDateTime = date("Y-m-d H:i:s");
                                $updatedDate = date("Y-m-d H:i:s", strtotime($chargeData->updated_at));
                                $createdDate = date("Y-m-d H:i:s", strtotime($chargeData->created_at));
                                $billingDate = date("Y-m-d H:i:s", strtotime($chargeData->billing_on));
                                // check for charge which current date is greater than updated date and created date is less than 30 days
                               // logger('Current Date :: ' . $curDateTime);

                                $charge = Charge::where('charge_id', $chargeData->id)->where('status', 'ACTIVE')->first();

                                logger('============= charge ============');
                                logger(json_encode($charge));
                                if( $charge ){

                                 //   logger((strtotime($updatedDate) <  strtotime($curDateTime)) && (strtotime($createdDate) < strtotime('-30 days')));

                                    if( (strtotime($updatedDate) <  strtotime($curDateTime)) && (strtotime($createdDate) < strtotime('-30 days'))){
                                        $counter = Counters::where('charge_id', $charge->id)->where('status', 'active')->first();
                                        logger('============= counter ============');
                                       // logger(json_encode($counter));
                                        if( $counter ){
                                            DB::beginTransaction();
                                            if( strtotime($counter->start_date) < strtotime('-30 days') ){

                                                $counter->status = 'canceled';
                                                $counter->save();

                                                $newCounter = new Counters;
                                                $newCounter->user_id = $counter->user_id;
                                                $newCounter->charge_id = $counter->charge_id;
                                                $newCounter->plan_id = $counter->plan_id;
                                                $newCounter->regular_product_count = 0;
                                                $newCounter->regular_product_variant_count = 0;
                                                $newCounter->affiliate_product_count = 0;
                                                $newCounter->affiliate_product_variant_count = 0;
                                                $newCounter->auto_update_count = 0;
                                                $newCounter->auto_update_affiliate = 0;
                                                $newCounter->status = 'active';
                                                $newCounter->start_date = date('Y-m-d H:i:s',strtotime($chargeData->updated_at));
                                                $newCounter->end_date = date('Y-m-d H:i:s',strtotime($newCounter->start_date.' + 30 days'));
                                                $newCounter->save();

                                                    logger('===== TrakChargeJob::Counter :: Shop ID :: ' .$counter->user_id .' ========');

                                                logger('============= update billing_on ============');
                                                $charge->billing_on = $billingDate;
                                                $charge->save();
                                                logger('============= updated billing_on ============');

                                            }

                                            DB::commit();
                                        }
                                    }
                                }
                            }
                         }
                       }
                      }
                    }
                }
            }
        }catch( \Exception $e ){
            DB::rollBack();
            logger('========== ERROR:: TrackChargeJob =========');
            logger($e);
        }
    }

    public function getShop($user){
        try{
            logger('================= START:: getShop =================');
           // logger('=========== USER =========');
           // logger(json_encode($user));

            $query = '{
                          shop {
                            id,
                            name
                          }
                    }';
            $result = $this->graph($user, $query);
//            $endPoint = 'admin/api/' . env('SHOPIFY_API_VERSION') . '/shop.json';
//            $parameter['fields'] = 'id';
//            logger($endPoint);
//            $result = $user->api()->rest('GET', $endPoint, $parameter);

           // logger('=========== SH Shop =========');
           // logger(json_encode($result));

            if( isset($result->errors) ){
                return $result->errors;
            }else{
                return true;
            }
            logger('================= END:: getShop =================');
        }catch( \Exception $e ){
            logger('================= ERROR:: getShop =================');
            logger($e->getMessage());
            return true;
        }
    }
}
