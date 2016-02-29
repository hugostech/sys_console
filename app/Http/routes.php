<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return redirect('list');
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});
Route::post('warranty','warrantyController@store');
Route::get('warranty','warrantyController@show');
Route::get('list','warrantyController@listOnGoing');
Route::get('listAll','warrantyController@listAll');
Route::get('listFinish','warrantyController@listFinish');
Route::put('list','warrantyController@search');
Route::post('step3','warrantyController@step3');
Route::post('step4','warrantyController@step4');
Route::get('ship/{shipno}','warrantyController@checkShip');
Route::get('finishWarranty/{id}','warrantyController@finish');
Route::get('warranty/{id}','warrantyController@detail');
Route::post('warranty/{id}','warrantyController@addNote');
Route::post('supplier','adminController@storeSupplier');
Route::get('supplier','adminController@showSupplier');
Route::get('supplier/{id}','adminController@editSupplier');
Route::put('supplier/{id}','adminController@updateSupplier');
Route::get('suppliers','adminController@listSuppliers');
Route::get('print/{id}','warrantyController@printWarranty');
Route::put('warranty/{id}','warrantyController@updateWarranty');
