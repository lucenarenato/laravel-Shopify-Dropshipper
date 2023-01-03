@extends('layouts.app')

@section('title', ' | My Products')

@section('content')
    <div class="d-flex w-100 justify-content-between">
        <div style="width: 60%;">

            <h1> {{isset($page_title) ? $page_title : "My Products" }}</h1>

            <p style="color: red;">
                {{isset($page_contents['top_right_note_text']) ? $page_contents['top_right_note_text'] : "If after the recent App upgrade you cannot see your products in this page, please email us your store URL including the .myshopify.com to info@AmaZoneDropshipping.com" }}
            </p>
        </div>
        <div>
            <p class="my-product-cnt"><span class="my-product-cnt-fc">
                  {{isset($page_contents['top_left_text1']) ? $page_contents['top_left_text1'] : "Current Plan: " }}
                </span><span class="my-product-cnt-lc">{{$counter['current_plan']}}</span></p>
            <p class="my-product-cnt"><span class="my-product-cnt-fc">
                 {{isset($page_contents['top_left_text2']) ? $page_contents['top_left_text2'] : "Products Imported this Month: " }}
                </span><span class="my-product-cnt-lc">{{$counter['total_product']}}</span></p>
            <p class="my-product-cnt"><span class="my-product-cnt-fc">
                  {{isset($page_contents['top_left_text3']) ? $page_contents['top_left_text3'] : "Auto Updates this month: " }}                   </span><span class="my-product-cnt-lc">{{$counter['total_auto_product']}}</span></p>
            <p class="my-product-cnt"><span class="my-product-cnt-fc">
                 {{isset($page_contents['top_left_text4']) ? $page_contents['top_left_text4'] : " Affiliate Products Imported this Month: " }}   </span><span class="my-product-cnt-lc">{{$counter['total_affiliate_product']}}</span></p>
        </div>
    </div>


    <products-manager></products-manager>
@endsection
