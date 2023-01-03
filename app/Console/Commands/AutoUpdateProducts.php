<?php

namespace App\Console\Commands;

use App\Models\Counters;
use App\Traits\AutoUpdate;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoUpdateProducts extends Command
{
    use AutoUpdate;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autoupdate:products {product_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try{

            logger("Start Auto update product Cron Job");

            $productid = $this->argument('product_id');
            $product = \DB::table('products')->where('id', $productid)->first();
            $shop = User::find($product->user_id);

             if( $shop ){
                $charge = DB::table('charges')->where('status', 'ACTIVE')->where('user_id', $shop->id)->orderBy('created_at', 'desc')->first();
                $counter = Counters::where('user_id', $shop->id)->where('status', 'active')->where('charge_id',
                    $charge->id)->first();
                $plan_detail = DB::table('plans')->where('id', $shop->plan_id)->first();

                $affiliate = json_decode($product->affiliate);

                $plan['err'] = 0;
                if ($affiliate->status) {
                    if ($plan_detail->max_affiliate_product_import != 'unlimited') {
                        $plan['err'] = ($plan_detail->max_affiliate_product_import != 'unlimited' && ($counter->affiliate_product_count >= $plan_detail->max_affiliate_product_import)) ? 1 : 0;
//                    $plan['err'] = ($plan_detail->max_affiliate_product_import != 'unlimited' && ($counter->affiliate_product_count + $counter->auto_update_affiliate) >= $plan_detail->max_affiliate_product_import) ? 1 : 0;
                    }
                }else{
//                $plan['err'] = ($counter->regular_product_count + $counter->auto_update_count) >= $plan_detail->max_regular_product_import ? 1 : 0;
                    $plan['err'] = ($counter->regular_product_count >= $plan_detail->max_regular_product_import) ? 1 : 0;

                }

                if( $plan['err'] == 0 ){
                    $autoUpdate = json_decode($product->auto_update);
                    $frequency = (int)$autoUpdate->frequency;

                    $is_old_key = ($frequency == 0);
                    $this->index($productid, $is_old_key);
                }

                // $products = DB::table('products')->whereBetween('updated_at', ['2021-01-22 00:00:00', '2021-01-22 23:59:59'])->get();
                //  foreach ($products as $key => $value) {
                //      $this->index($value->id);
                // }

             }
            //logger('=============== END:: Auto-update Product =============');
        }catch( \Exception $e ){
            logger('=============== ERROR:: Auto-update Product =============');
            logger($e);
        }
    }
}
