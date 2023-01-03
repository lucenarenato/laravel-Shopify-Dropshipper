<?php namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Osiset\ShopifyApp\Contracts\Objects\Values\ShopDomain;
use stdClass;

class ProductsDeleteJob implements ShouldQueue
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
    public function handle()
    {
        try{
 logger("=============== Product Create Job ===============");
            $shop_domain = $this->shopDomain->toNative();
            $shop = DB::table('users')->where('name', $shop_domain)->first();

 logger("Domain shop".$shop_domain);

             logger("user".json_encode($shop));
             
            if($shop){

            $sh_product_id = $this->data->id;
            $product = DB::table('products')->where('user_id', $shop->id)->where('shopify_id', $sh_product_id)->first();


            if( $product ){
                $productID = $product->id;

            //\Log::info('===== Delete Product Webhook :: Shop ID :: ' .$shop->id .' Product ID :: '.$productID.' ========');

                $variantRecs = DB::table('variants')->where('product_id', $productID)->get();

                // Delete attributes
                foreach ($variantRecs as $vRec) {
                    DB::table('attributes')->where('variant_id', $vRec->id)->delete();
                }

                // Delete variants
                DB::table('variants')->where('product_id', $productID)->delete();

                // Delete images
                DB::table('images')->where('product_id', $productID)->delete();

                // Delete tags
                DB::table('tags')->where('product_id', $productID)->delete();

                // Delete product rec
                DB::table('products')->where('id', $productID)->delete();

            }
        }

            //\Log::info('===================== Product Deleted successsfully =======================');
        }catch( \Exception $e ){
            \Log::info('ERROR :: Delete Product Webhook');
            \Log::info($e);
        }
        // Do what you wish with the data
        // Access domain name as $this->shopDomain->toNative()
    }
}
