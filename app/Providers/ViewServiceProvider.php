<?php

namespace App\Providers;

use App\Models\cmsFaq;
use App\Models\CmsGlobalContent;
use App\Models\CmsPages;
use App\Models\CmsSideBarMenu;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;


class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        //   START :: cms_global_contents and cms_side_bar_menus================

                view()->composer('layouts.app', function ($view)
                {
                    $CmsSideBarMenu = CmsSideBarMenu::all();
                    $CmsGlobalContent = CmsGlobalContent::first();

                    $view->with('sidebar_contents',$CmsSideBarMenu);
                    $view->with('global_content',$CmsGlobalContent);

                });

        //   END :: cms_global_contents and cms_side_bar_menus==================



        //   START :: Page :: import-products===============================

                view()->composer('import', function ($view)
                {
                    $records = CmsPages::with('CmsPagesContent')->where('page_slug','=','import-products')->first();

                    $CmsPagesContent = $records->CmsPagesContent->keyBy('field_slug')->pluck('field_value','field_slug')->toArray();

                    $view->with('page_title',$records->page_title);
                    $view->with('page_contents',$CmsPagesContent);

                });

        //   END :: Page :: import-products==================================



        //   START :: Page :: my-products===============================

        view()->composer('products', function ($view)
        {
            $records = CmsPages::with('CmsPagesContent')->where('page_slug','=','my-products')->first();

            $CmsPagesContent = $records->CmsPagesContent->keyBy('field_slug')->pluck('field_value','field_slug')->toArray();

            $view->with('page_title',$records->page_title);
            $view->with('page_contents',$CmsPagesContent);

        });

        //   END :: Page :: my-products==================================




        //   START :: Page :: my-orders===============================

        view()->composer('orders', function ($view)
        {
            $records = CmsPages::with('CmsPagesContent')->where('page_slug','=','my-orders')->first();

            $CmsPagesContent = $records->CmsPagesContent->keyBy('field_slug')->pluck('field_value','field_slug')->toArray();

            $view->with('page_title',$records->page_title);
            $view->with('page_contents',$CmsPagesContent);

        });

        //   END :: Page :: my-orders==================================



        //   START :: Page :: help-center===============================

        view()->composer('help', function ($view)
        {
            $records = CmsPages::with('CmsPagesContent')->where('page_slug','=','help-center')->first();

            $CmsPagesContent = $records->CmsPagesContent->keyBy('field_slug')->pluck('field_value','field_slug')->toArray();

            $view->with('page_title',$records->page_title);
            $view->with('page_contents',$CmsPagesContent);

        });

        //   END :: Page :: help-center==================================





        //START :: Page :: FAQ========================================
        view()->composer(['faqs','help'], function ($view) {

            $faqRecords = cmsFaq::with('cmsFaqContent')->get();
            $view->with('faqs_page_contents', $faqRecords);

        });

        //END :: Page :: FAQ========================================




        //   START :: Page :: settings===============================

        view()->composer('settings', function ($view)
        {
            $records = CmsPages::with('CmsPagesContent')->where('page_slug','=','settings')->first();

            $CmsPagesContent = $records->CmsPagesContent->keyBy('field_slug')->pluck('field_value','field_slug')->toArray();

            $view->with('page_title',$records->page_title);
            $view->with('page_contents',$CmsPagesContent);

        });

        //   END :: Page :: settings==================================


    }
}
