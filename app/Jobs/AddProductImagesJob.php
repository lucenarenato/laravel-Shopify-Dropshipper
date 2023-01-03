<?php

namespace App\Jobs;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddProductImagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $shop_id = '';
    private $images = '';
    private $shopifyId = '';
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($shop_id, $images, $shopifyId)
    {
        $this->shop_id = $shop_id;
        $this->images = $images;
        $this->shopifyId = $shopifyId;
//        $this->handle();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
           // \Log::info("===================START:: AddProductImagesJob=================");

            $shop = User::where('id', $this->shop_id)->first();
            $images = $this->images;
            foreach ( $images as $key=>$val ){
                $img = [
                    'image' => $val,
                ];
                $endPoint = '/admin/api/'. env('SHOPIFY_API_VERSION') .'/products/'. $this->shopifyId .'/images.json';
                $result = $shop->api()->rest('POST', $endPoint, $img);   // shopify images result.

//                \Log::info(json_encode($result));
            }

//            \Log::info("===================END:: AddProductImagesJob=================");
        }catch(\Exception $e){
            \Log::info("===================ERROR:: AddProductImagesJob=================");
            \Log::info($e);
        }
    }
}
