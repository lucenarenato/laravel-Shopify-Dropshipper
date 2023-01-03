<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::group(['middleware' => ['cors'], 'namespace' => 'Api'], function () {
    Route::get('check-affiliate', 'ProductController@checkAffiliate');
    Route::get('webhook/set/track-product', 'ProductController@keepaTrackProductWebhookSet');  // https://amazon-dropshipper.test/api/webhook/set/track-product
    Route::POST('webhook/track-product', 'ProductController@keepaTrackProductWebhook')->name('keepa.webhook'); // https://amazon-dropshipper.test/api/webhook/track-product

    Route::get('keepa/name-list', 'ProductController@keepaListNames'); // https://amazon-dropshipper.test/api/keepa/name-list
    Route::POST('keepa/get/track-product/', 'ProductController@getProductTracking'); //https://amazon-dropshipper.test/api/keepa/get/track-product

   // https://amazonedropshipping.crawlapps.com/api/keepa/name/list

    Route::POST('keepa/add/track-product/', 'ProductController@addProductTracking'); //https://amazonedropshipping.crawlapps.com/api/keepa/add/track-product

});

//Route::post('/get-amazon-product', 'AppController@getAmazonProduct');
//Route::post('/get-walmart-product', 'AppController@getWalmartProduct');
//Route::post('/get-collections', 'AppController@getCollections');
//Route::post('/add-product', 'AppController@addProduct')->middleware(['auth.shopify']);


