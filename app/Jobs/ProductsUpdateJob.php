<?php namespace App\Jobs;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Osiset\ShopifyApp\Contracts\Objects\Values\ShopDomain;
use stdClass;

class ProductsUpdateJob implements ShouldQueue
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
           logger('====================== START:: ProductsUpdateJob  ======================');
            $domain = $this->shopDomain->toNative();
            $user = User::where('name', $domain)->first();
 
             logger("Domain shop".$domain);

             logger("user".json_encode($user));
             
            if($user){
            $data = $this->data;
            $sh_productID = $data->id;

            $db_product = DB::table('products')->where('shopify_id', $sh_productID)->where('user_id', $user->id)->first();
            $sh_variants = $data->variants;
            $tags = $data->tags;

            if( $db_product ){
                if( count($sh_variants) > 1 ){
                    foreach ( $sh_variants as $svkey=>$svval ){
                        $db_variant = DB::table('variants')->where('product_id', $db_product->id)->where('source_id', $svval->sku)->update(['shopify_price' => $svval->price, 'updated_at' => date('Y-m-d H:i:s')]);
                    }
                }else{
                    if( count($sh_variants) == 1 ){
                        // $db_variant = DB::table('variants')->where('product_id', $db_product->id)->update(['shopify_price' => $sh_variants[0]->price, 'source_price' => $sh_variants[0]->price, 'updated_at' => date('Y-m-d H:i:s')]);
                    }
                }


                if (!Empty($tags)) {
                  //  logger("Product Tags saving... :: ProductsUpdateJob");
                    DB::table('tags')->where('product_id',$db_product->id)->delete();

                   // logger($tags);

                    $tagsData = explode(",",$tags);

                    if(count($tagsData)>0){
                        for($t=0; $t<count($tagsData); $t++){

                            $tag_val = trim($tagsData[$t]);

                            DB::table('tags')->insert(['product_id' => $db_product->id, 'tag' => $tag_val]);
                        }
                    }

                }
              }
          
            }
//            logger('====================== END:: ProductsUpdateJob  ======================');
        }catch( \Exceptation $e){
             logger('====================== ERROR:: ProductsUpdateJob  ======================');
             logger(json_encode($e));
        }

    }
}
