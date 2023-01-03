<?php

namespace App\Listeners;

use App\Events\CheckOrder;
use App\Mail\ReceivesOrderMail;
use App\Models\LineItem;
use App\Models\Order;
use App\Product;
use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Intl\Currencies;

class OrderCreateUpdate
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
     * @param  CheckOrder  $event
     * @return void
     */
    public function handle(CheckOrder $event)
    {
        try {
            //logger('========== Listener:: Order create Update ==========');
            $ids = $event->ids;
            $user_id = $ids['user_id'];
            $data = $ids['data'];

            $sh_product_ids = [];
            $sh_lineItems = [];
            if( !empty( $data->line_items ) ){
                $sh_lineItems = $data->line_items;
                foreach ( $sh_lineItems as $lkey=>$lval ){
                    $sh_product_ids[] = $lval->product_id;
                }
            }

            $is_exist_product = Product::where('user_id', $user_id)->whereIn('shopify_id', $sh_product_ids)->count();

            if( $is_exist_product > 0 ){
                $is_exist_order = Order::where('shopify_order_id', $data->id)->where('user_id', $user_id)->first();

                $db_order = ( $is_exist_order ) ? $is_exist_order : new Order;
                $db_order->user_id = $user_id;
                $db_order->shopify_order_id = $data->id;
                $db_order->name = $data->name;
                $db_order->client_name = (@$data->shipping_address->name) ? $data->shipping_address->name : '';
                $db_order->email = (@$data->email) ? $data->email : '';
                $db_order->address1 = (@$data->shipping_address->address1) ? $data->shipping_address->address1 : '';
                $db_order->address2 = (@$data->shipping_address->address2) ? $data->shipping_address->address2 : '';
                $db_order->zip = (@$data->shipping_address->zip) ? $data->shipping_address->zip : '';
                $db_order->city = (@$data->shipping_address->city) ? $data->shipping_address->city : '';
                $db_order->country = (@$data->shipping_address->country) ? $data->shipping_address->country : '';
                $db_order->phone = (@$data->shipping_address->phone) ? $data->shipping_address->phone : '';
                $db_order->fulfillment_status = (@$data->fulfillment_status) ? $data->fulfillment_status : 'pending';
                $db_order->save();

                $lineItems = LineItem::where('db_order_id', $db_order->id)->get();
                if( $lineItems ){
                    foreach ( $lineItems as $key=>$val ){
                        $val->delete();
                    }
                }
                if( !empty( $sh_lineItems ) ){
                    foreach ( $sh_lineItems as $lkey=>$lval ){
                        $sh_product_id = $lval->product_id;
                        $exist_p = Product::where('shopify_id', $sh_product_id)->where('user_id', $user_id)->first();
                        if( $exist_p ){

                            if( $lval->sku != '' ){
                                $db_variant = \DB::table('variants')->where('product_id', $exist_p->id)->where('source_id', $lval->sku)->first();
                            }else{
                                $db_variant = \DB::table('variants')->where('product_id', $exist_p->id)->first();
                            }

                            $sh_product = $this->shProduct($user_id, $sh_product_id);
                            $lineItem = new LineItem;
                            $lineItem->db_order_id = $db_order->id;
                            $lineItem->shopify_lineitem_id = $lval->id;
                            $lineItem->shopify_product_id = $lval->product_id;
                            $lineItem->shopify_variant_id = $lval->variant_id;
                            $lineItem->variant_title = $lval->variant_title;
                            $lineItem->product_title = $sh_product['title'];
                            $lineItem->image = $sh_product['image'];
                            $lineItem->source = $exist_p->source;
                            $lineItem->source_url = $exist_p->source_url;
                            $lineItem->sku = $lval->sku;
                            $lineItem->currency = $this->storeCurrency($user_id);
                            $lineItem->last_price = (@$db_variant) ? $db_variant->source_price : $lval->price;
                            $lineItem->sold_price = $lval->price;
                            $lineItem->quantity = $lval->quantity;
                            $lineItem->save();
                        }
                    }
                }
                $user = User::find($user_id);

//                $total_created_order = Order::where('user_id', $user_id)->get();
//
//                if(!$is_exist_order) {
//                    if (count($total_created_order) < 5) {
//                        $to_mail = $user->email;
//
//                        logger("Email recevier :: ".$to_mail);
//
//                        // $to_mail = "satish.crawlapps@gmail.com";
//                        if (!empty($to_mail)) {
//                            $params = [];
//                            Mail::to($to_mail)->send(new ReceivesOrderMail($params));
//                        }
//
//                    }
//                }


            }else{
                $db_order = Order::where('shopify_order_id', $data->id)->where('user_id', $user_id)->get();
                if( count($db_order ) > 0 ){
                    foreach ( $db_order as $dkey=>$dval ){
                        $lineItems = LineItem::where('db_order_id', $dval->id)->get();
                        if( count( $lineItems ) > 0 ){
                            foreach ( $lineItems as $lkey=>$lval ){
                                $lval->delete();
                            }
                        }
                        $dval->delete();
                    }
                }
            }
        }catch ( \Exception $e ){
            logger('========== ERROR:: Listener:: Order create Update ==========');
            logger($e);
        }
    }

    // fetch native store currency
    public function storeCurrency($user_id){
        $user = User::find($user_id);
        $parameter['fields'] = 'currency';
        $shop_result = $user->api()->rest('GET', 'admin/api/' . env('SHOPIFY_API_VERSION') . '/shop.json', $parameter);

        $currency = '';
        if( !$shop_result->errors ){
            return Currencies::getSymbol($shop_result->body->shop->currency);
        }
        return $currency;
    }

    //fetch shopify product title
    public function shProduct($user_id, $product_id){
        try{
            $user = User::find($user_id);
            $parameter['fields'] = 'id,title,image';
            $result = $user->api()->rest('GET', 'admin/'. env('SHOPIFY_API_VERSION') .'/products/'. $product_id .'.json', $parameter);

            $product['title'] = '';
            $product['image'] = '';
            $title = '';
            if( !$result->errors ){
                $sh_product = $result->body->product;
                $product['title'] = $sh_product->title;
                $product['image'] =  (@$sh_product->image->src) ? $sh_product->image->src : noImagePathH();
            }
            return $product;
        }catch( \Exception $e ){
            logger('============ ERROR:: Product ==========');
          //  logger(json_encode($e));
        }
    }
}
