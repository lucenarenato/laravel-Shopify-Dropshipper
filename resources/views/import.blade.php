@extends('layouts.app')

@section('title', ' | Import Products')

@section('content')

    <div class="import-page">
    <div class="import-top-info">

        <p><span class="import-pre-text">{{\Carbon\Carbon::now()->format('M d')}}:</span>
            {{isset($page_contents['import_top_info_text_1']) ? $page_contents['import_top_info_text_1'] : "Walmart Import Issue is being investigated." }}
        </p>
        <p><span class="import-pre-text">{{\Carbon\Carbon::now()->format('M d')}}:</span>
            {{isset($page_contents['import_top_info_text_2']) ? $page_contents['import_top_info_text_2'] : "Download our new Chrome Extenstion to Import with  1 click and Auto fulfill orders. (soon)" }}
        </p>
{{--        <div class="import-divider"></div>--}}
    </div>


@if($page_contents['section2_top_title']!==null && $page_contents['section2_top_title']!=='')
        <section>
        <div class="row text-center" style="width: 779px;margin: auto;">

            <div class="col-md-12">
            <h5 class="card-title">{{$page_contents['section2_top_title']}}</h5>
            </div>

            <div class="col-md-6">
               <a href="{{$page_contents['section2_col1_link']}}" target="_blank" style="text-decoration: none;">
                <div class="card mb-3" style="max-width: 540px;border: none;">
                    <div class="row g-0">
                        <div class="col-md-4" style="display: flex;justify-content: center;align-items: center;">
                            @if($page_contents['section2_col1_image']!==null && $page_contents['section2_col1_image']!=='')
                            <img src="{{asset($page_contents['section2_col1_image'])}}" class="img-fluid rounded-start" alt="...">
                            @endif
                        </div>
                        <div class="col-md-8">
                            <div class="card-body" style="text-align: left;padding-left: 0px;">
                                <h5 class="card-title" style="color: #eda90c;">{{$page_contents['section2_col1_heading']}}</h5>
                                <p class="card-text" style="color: black;">{{$page_contents['section2_col1_sub_title']}}</p>
                                <div class="card-text" style="color: black;"> {!! $page_contents['section2_col1_content'] !!}</div>
                            </div>
                        </div>
                    </div>
                </div>
                </a>
            </div>

           <div class="col-md-6">
               <a href="{{$page_contents['section2_col2_link']}}" target="_blank" style="text-decoration: none;">
                   <div class="card mb-3" style="max-width: 540px;border: none;">
                       <div class="row g-0">
                           <div class="col-md-4" style="display: flex;justify-content: center;align-items: center;">
                            @if($page_contents['section2_col2_image']!==null && $page_contents['section2_col2_image']!=='')
                                   <img src="{{asset($page_contents['section2_col2_image'])}}" class="img-fluid rounded-start" alt="...">
                            @endif
                           </div>
                           <div class="col-md-8">
                               <div class="card-body" style="text-align: left;padding-left: 0px;">
                                   <h5 class="card-title" style="color: #eda90c;">{{$page_contents['section2_col2_heading']}}</h5>
                                   <p class="card-text" style="color: black;">{{$page_contents['section2_col2_sub_title']}}</p>
                                   <div class="card-text" style="color: black;">{!! $page_contents['section2_col2_content'] !!}</div>
                               </div>
                           </div>
                       </div>
                   </div>
               </a>
           </div>

        </div>
        </section>
@endif
<h3 class="import-search-heading"> {{isset($page_contents['import_search_heading']) ? $page_contents['import_search_heading'] : "Import and DropShip any Product from Amazon or Walmart" }}</h3>

        <div class="row justify-content-center import-page">
            <div class="col-md-9">
                <!--                <p>Enter product URL from Amazon or Walmart online store, then press the Get Product button.</p>-->
                <p class="sub-title">
                    {{isset($page_contents['import_search_sub_title']) ? $page_contents['import_search_sub_title'] : "Amazon US, CA, UK, DE, FR, SP, IT, BR, MX, IN and Walmart US" }}
                </p>
                <div class="import-title-divider"></div>
                <p class="import-guid-text">
                    {{isset($page_contents['import_guid_text1']) ? $page_contents['import_guid_text1'] : "Step 1: Go to Amazon or Walmart and Copy the Product Page URL. Step 2: Paste in the box below and click" }}
                </p>
                <p class="import-guid-text">
                    {{isset($page_contents['import_guid_text2']) ? $page_contents['import_guid_text2'] : "Get Product or download our Chrome extenstion and directy import from the Product page (soon)." }}
                </p>
            </div>
        </div>
                <product-importer :page_contents="{{json_encode($page_contents)}}"></product-importer>
    </div>



@endsection
