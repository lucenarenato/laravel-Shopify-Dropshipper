<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

if( env('APP_ENV') == 'local' ){
    Route::get('/', function(){
        return view('upgrade');
    });
    // return view('upgrade');
}else{
    Route::get('/app', function(){
    })->name('home')->middleware('auth.shopify','check.plan');
}

Route::get('/', function(){
    return view('front');
})->name('front');

Route::get('/import-home', function(){
})->name('import-home')->middleware('auth.shopify','check.plan');

Route::group(['namespace' => 'Migration'], function () {
    //user, plan, charges, product
    Route::get('amazon-migration/{table}', 'AmazonController@index');
});

Route::get('/create-plan', 'AppController@ActiveFreePlan')->name('create-plan')->middleware('auth.shopify');

Route::group(['middleware' => ['auth.shopify','billable']], function () {
    Route::get('/import', 'AppController@import')->name('import');
    Route::get('/check-plan-validity', 'AppController@checkValidatePlan');
    Route::get('/products', 'ProductsController@index');
    Route::post('/products/get-products', 'ProductsController@getProducts');
    Route::post('/products/get-products-variants', 'ProductsController@getProductsVariants');
    Route::post('/products/save-variants', 'ProductsController@saveVariants');
    Route::post('/products/delete-product', 'ProductsController@deleteProduct');
    Route::post('/products/update-autosetting', 'ProductsController@updateAutoSetting');
    Route::post('/get-amazon-product', 'AppController@getAmazonProduct');
    Route::post('/get-walmart-product', 'AppController@getWalmartProduct');
    Route::post('/get-collections', 'AppController@getCollections');
    Route::post('/get-collections-category', 'AppController@getCollectionsCategory');
    Route::post('/get-tags', 'AppController@getTags');
    Route::post('/get-shopify-tags', 'AppController@getShopifyTags');
    Route::post('/add-product', 'AppController@addProduct');
    Route::post('/search-product', 'AppController@getSearchProduct');
    Route::get('/get-reviews', 'ProductsController@getReview');
    Route::post('/change-show-review', 'ProductsController@changeShowReview');
    Route::get('/link-clicked', 'ProductsController@linkClicked');

    Route::get('/orders', 'OrdersController@index');
    Route::post('/orders/get-orders', 'OrdersController@getOrders');

    Route::get('/settings', 'SettingsController@index');
    Route::post('/settings/set-plan', 'SettingsController@setPlan');

    Route::get('/settings/get-amazon-associates', 'SettingsController@getAmazonAssociates');
    Route::post('/settings/add-amazon-associate', 'SettingsController@addAmazonAssociate');
    Route::post('/settings/delete-amazon-associate', 'SettingsController@deleteAmazonAssociate');

    Route::post('/settings/set-amazon-associate-btn', 'SettingsController@setAmazonAssociateBtn');
    Route::post('/settings/set-advanced', 'SettingsController@setAdvanced');

    Route::get('/help', 'HelpController@index');
    Route::get('/help/tutorials', 'HelpController@tutorials');
    Route::get('/help/contact', 'HelpController@contact');
    Route::get('/help/announcements', 'HelpController@announcements');

    Route::post('/send-mail-report-suggestion', 'AppController@sendMailReportSuggestion');

    //Route::get('/notifications', 'AppController@notifications')->middleware(['auth.shopify']);

    Route::get('/test/receives-order-mail', [TestController::class, 'testReceivesOrderMail']);
    Route::get('/test/track-charge', [TestController::class, 'trackChargeCron']);
    Route::get('/test/delete-webhook/{id}', [TestController::class, 'deleteRegisterWebhook']);


});

Route::get('flush', function(){
    request()->session()->flush();
});




//START::Admin

Auth::routes();

//Route::get('/admin', 'Admin\DashboardController@Dashboard')->middleware('auth:admin');


Route::get('/login/admin', 'Auth\LoginController@showAdminLoginForm')->name('admin.login');
Route::post('/login/admin', 'Auth\LoginController@authenticate')->name('admin.authenticate');

Route::group(['prefix' => 'admin','middleware' => ['auth:admin'],'namespace'=>'Admin'], function(){

        Route::get('/', 'DashboardController@Dashboard')->name('admin.dashboard');

        Route::post('/global-content/{id}', 'DashboardController@saveGlobalContent')->name('admin.save.globalContent');
        Route::post('/side-bar-menu', 'DashboardController@saveSideBarMenu')->name('admin.save.sideBarMenu');

        Route::get('/import', 'ImportController@index')->name('admin.import');
        Route::post('/import/{id}', 'ImportController@update')->name('admin.import.update');

        Route::get('/products', 'ProductsController@index')->name('admin.products');
        Route::post('/products/{id}', 'ProductsController@update')->name('admin.products.update');

        Route::get('/orders', 'OrdersController@index')->name('admin.orders');
        Route::post('/orders/{id}', 'OrdersController@update')->name('admin.orders.update');

        Route::get('/settings', 'SettingsController@index')->name('admin.settings');
        Route::post('/settings/{id}', 'SettingsController@update')->name('admin.settings.update');

        Route::get('/help', 'HelpController@index')->name('admin.help');
        Route::post('/help/{id}', 'HelpController@update')->name('admin.help.update');

        Route::get('/faqs', 'FaqsController@index')->name('admin.faqs');
        Route::post('/faqs', 'FaqsController@update')->name('admin.faqs.update');

    Route::post('/remove-image-field-value/{id}', 'DashboardController@removeImageFieldValue')->name('admin.remove.image');

});

//END::Admin


