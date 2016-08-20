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
Route::get('step2/{id}','warrantyController@step2');
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

Route::get('getsn/{sn}','warrantyController@getSn');
Route::get('getDesc/{code}','warrantyController@getDesc');

Route::get('getTrackInfo/{id}',function($id){
    $warranty = \App\Warranty::find($id);
    $notes = $warranty->note;
//    var_dump($notes);
    foreach($notes as $note){
        $pos = strpos($note->note,'Delivery to supplier');
        if($pos !== false){
            echo $note->note;
            return;
        }
    }

});

Route::get('killprice','unilityController@killshow');
Route::post('killprice','unilityController@killPrice');
Route::put('killprice','unilityController@killPrice_edit');
Route::get('addWarrantyGuide',function (){
    $suppliers = \App\Category::all();
    $category_warrantys = \App\Category_warranty::all();
    return view('category_warranty',compact('suppliers','category_warrantys'));
});

Route::patch('addWarrantyGuide',function (\Illuminate\Http\Request $request){
    $category = \App\Category::create($request->all());
    return redirect(url('addWarrantyGuide'));
});

Route::put('addWarrantyGuide',function(\Illuminate\Http\Request $request){
    $category_warranty = \App\Category_warranty::create($request->all());
    return redirect(url('addWarrantyGuide'));
});

Route::post('addWarrantyGuide',function(\Illuminate\Http\Request $request){
    $inputs = $request->all();

    echo $category_id = $inputs['category_id'];
    \App\category_item::where('category_id',$category_id)->delete();
    foreach($inputs as $key=>$input){
        if(strpos($key, 'warranty_id') !== false){
            echo 'asd';
            $category = new \App\category_item();
            $category->category_id = $category_id;
            $category->warranty_detail_id = $input;
            $category->save();
        }
    }
    return redirect(url('addWarrantyGuide'));
});

Route::get('warrantyGuide','unilityController@showWarrantyGuide');
Route::get('warrantyGuide/{id}','unilityController@warrantySubCategory');
Route::get('warrantydetail/{id}','unilityController@warrantydetail');

Route::get('sync','unilityController@showSync');
Route::post('sync','unilityController@sync');
Route::get('self_sync','unilityController@dailySync');
Route::get('old_data','unilityController@old_transfer');

Route::get('unsubscribe/{email}','unilityController@unsubscribe');
Route::get('syncpro/{code}','unilityController@addNewProduct');
Route::get('syncproall','unilityController@grabProducts');
Route::get('feed','unilityController@productFeed');
Route::get('tsafeed','unilityController@tsaFeed');
Route::get('syncqty','unilityController@syncqty');
Route::get('checkorder','unilityController@checkOrder');
Route::get('relatedproduct','unilityController@relatedproduct');
Route::get('categoryArrange','unilityController@categoryarrange');
Route::get('showauckland','unilityController@showAucklandCustomer');
Route::get('saveClient','unilityController@addNewClient');
Route::get('createorder/{id}','unilityController@createRoctechOrder');
Route::get('changeOrderStatus','unilityController@changeOrderStatus');
Route::get('eta_list','unilityController@eta_list');
Route::post('eta_list','unilityController@eta_add');
Route::get('eta_remove/{id}','unilityController@eta_remove');
Route::get('sales_list','unilityController@sales_list');
Route::post('sales_list','unilityController@sales_add');
Route::get('sales_remove/{id}','unilityController@sales_remove');

Route::get('testEmail','unilityController@producttosales');
Route::get('categoryaddaoc','unilityController@addtoaoc');
Route::get('cloneCategory/{c1}/{c2}','unilityController@addtoaoc');




