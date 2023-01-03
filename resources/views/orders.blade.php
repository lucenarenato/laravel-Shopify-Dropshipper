@extends('layouts.app')

@section('title', ' | My Orders')

@section('content')
<h1> {{isset($page_title) ? $page_title : "My Orders" }}</h1>
<p>
    {{isset($page_contents['top_text1']) ? $page_contents['top_text1'] : "Here you can view the orders for your imported products. You can see Amazone/Walmart Link, Shopify Link, Client Details, Order Details, Price." }}
</p>

    @if(isset($page_contents['top_text2_content']))
        {!! $page_contents['top_text2_content']  !!}
    @else
        <p style="color: #841111;"> The user is responsible for placing and fulfilling your orders. Please <a href="FAQ.php#orderhandling" target="_blank">read the FAQ section 'Order Handling' or click here</a> for more info. If you have questions please visit our FAQ section or Contact Us.</p>
   @endif

<orders-manager></orders-manager>
@endsection
