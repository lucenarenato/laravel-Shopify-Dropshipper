<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">


        <!-- Scripts -->
        <script src="{{ mix('js/app.js') }}" defer></script>

        <!-- Styles -->
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">
        <link rel="icon" href="{{asset('/images/favicon.ico')}}">

        <?php
        header("Content-Security-Policy: frame-ancestors https://".auth()->user()->name."  https://admin.shopify.com");
        ?>

    </head>
    <body class="c-app">
        <div id="app">
            @include('layouts.sidebar')
            <div class="c-wrapper">
                @include('layouts.header')
                <div class="c-body">
                    <main class="c-main">
                        <div class="container-fluid">
                            <div id="ui-view">
                                <div>
                                    <div class="fade-in">
                                        @yield('content')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
                <footer class="c-footer">
                    <div>
                        @if(isset($global_content))
                            <a href="{{$global_content->footer_cpr_link}}">{{$global_content->footer_cpr_text1}}</a> {{$global_content->footer_cpr_text2}}
                        @else
                            <a href="https://amazonedropshipper.com">AmaZone Dropshipper</a> © {{date('y')}} AmaZone Dropshipper.
                        @endif
                    </div>
{{--                    <div class="mfs-auto">Powered by&nbsp;<a href="https://rubiconlabs.com">Rubicon Labs</a></div>--}}
                </footer>
            </div>
        </div>

    </body>


</html>
