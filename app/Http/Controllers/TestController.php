<?php

namespace App\Http\Controllers;

use App\Mail\ProductReportSuggestion;
use Illuminate\Http\Request;
use DB;
use Auth;
use App\Mail\ReceivesOrderMail;
use Illuminate\Support\Facades\Mail;
use Artisan;
use Exception;
use App\User;


class TestController extends Controller
{
    public function testReceivesOrderMail(Request $request){

       // logger("===============STRAT::TEST::MODE======================");

       //example email -1

//        $params = [
//                'store_name'=> "test",
//            ];

        //return view('emails.receives-order',['params' => $params]);


        //example email -2

        $params = [
            "user_plan" => "GOLD",
            "app_uninstall_date" => "2021-12-02",
            "first_day_of_paid_plan" => 20
        ];

        return view('emails.app-uninstalled-mail',['params' => $params]);


        $to_mail = env('MAIL_TO_ADDRESS');

        Mail::to($to_mail)->send(new ReceivesOrderMail($params));

        return ['success' => true, "message" => "Email sent Successfully"];

        return 0;

     //   logger("===============END::TEST::MODE======================");

    }


    public function trackChargeCron(Request $request){

       logger("===============STRAT::TEST::MODE track:charge======================");

            Artisan::call('track:charge');

            return 0;

        logger("===============END::TEST::MODE track:charge======================");


    }




    public function deleteRegisterWebhook(Request $request,$id){

        //$shop = Auth::user();

        $webhooks = webhooksList($id);

        dd($webhooks);

//        if(count($webhooks) > 0){
//
//           foreach($webhooks as $webhook){
//
//               $webhook_id = $webhook->id;
//
//               //START :: DELETE WEBHOOK
//
//               $webhookDeleteReq = $shop->api()->rest('DELETE', '/admin/api/'.env('SHOPIFY_API_VERSION').'/webhooks/'.$webhook_id.'.json');
//
//               logger("===============START :: webhooks :: DELETE Result=================");
//               logger(json_encode($webhookDeleteReq));
//               logger('================= END::  webhooks :: DELETE =================');
//
//               //END :: DELETE WEBHOOK
//
//           }
//
//        }
    }


}
