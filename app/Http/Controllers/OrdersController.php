<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrdersController extends Controller
{
    /**
     * Show Orders page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $store = Auth::user()->name;
        return view('orders',compact('store'));
    }

    public function getOrders(){
        try{
            $shop = Auth::user();
            $shopURL = 'https://' . $shop->getDomain()->toNative();

            $data['orders'] = Order::with('LineItems')->where('user_id', $shop->id)->get();
            $data['shop']['url'] = $shopURL;
            return ['success' => true, 'data' => $data];
        }catch( \Exception $e ){
            return [ 'success' => false, 'errors' => $e->getMessage()];
        }
    }
}
