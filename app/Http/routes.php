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
Route::get('ktechfeed','unilityController@ktechProductFeed');
Route::get('tsafeed','unilityController@tsaFeed');
Route::get('syncqty','unilityController@syncqty');
Route::get('checkorder','unilityController@checkOrder');
Route::get('relatedproduct','unilityController@relatedproduct');
Route::get('categoryArrange','unilityController@categoryarrange');
Route::get('showauckland','unilityController@showAucklandCustomer');
Route::get('saveClient','unilityController@addNewClient');
Route::get('createorder/{id}','unilityController@createRoctechOrder');
Route::get('changeOrderStatus','unilityController@changeOrderStatus');
Route::get('cleanOnSaleCategory','unilityController@cleanOnSaleCategory');
Route::get('eta_list','unilityController@eta_list');
Route::post('eta_list','unilityController@eta_add');
Route::get('eta_remove/{id}','unilityController@eta_remove');
Route::get('sales_list','unilityController@sales_list');
Route::get('flash_sale','unilityController@show_flash_sale');
Route::get('flash_sale_price_edit/{code}/{price}','unilityController@flash_sale_price_edit');
Route::get('flash_sale_rrp_edit/{code}/{rrp}','unilityController@flash_sale_rrp_edit');
Route::get('flash_sale_qty_edit/{code}/{qty}','unilityController@flash_sale_qty_edit');
Route::get('flash_sale_product_del/{id}','unilityController@flash_sale_product_del');
Route::post('add_flash_sale_product','unilityController@add_flash_sale_product');
Route::get('publishFlash','unilityController@publishFlash');
Route::get('offlineFlash','unilityController@offlineFlash');
Route::get('findMissProduct','unilityController@findMissProduct');
Route::post('dryCategory2Other','unilityController@dryCategory');


Route::post('sales_list','unilityController@sales_add');
Route::get('sales_remove/{id}','unilityController@sales_remove');
Route::get('laptop_attribute/{id}','unilityController@laptop_attribute');
Route::post('laptop_attribute','unilityController@insert_laptop_attribute');
Route::post('adminLogin','unilityController@adminLogin');


Route::get('testEmail','unilityController@producttosales');
Route::get('listClient','unilityController@listnewclient');
Route::get('categoryaddaoc','unilityController@addtoaoc');
Route::get('cloneCategory/{c1}/{c2}','unilityController@cloneCategoryA2CategoryB');

Route::get('listProductFromCategory','unilityController@listProductFromCategory');
Route::get('signProduct2Category/{product_id}','unilityController@signProduct2Category');
Route::get('deleteProductFromCategory/{category}/{product_id}','unilityController@deleteProductFromCategory');
Route::post('saveProduct2Category','unilityController@saveProduct2Category');
Route::post('batchPriceEdit','unilityController@batchPriceEdit');
Route::get('batchEditPrice/{category_id}','unilityController@batchEditPrice');
Route::get('christmas_sale_setting','unilityController@christmas_sale_setting');
Route::get('putProducts2Base','unilityController@putProducts2Base');

/*Label*/
Route::get('createLabel','labelController@show_label_tool');
Route::post('findLabel','labelController@findLabel');
Route::post('editLabel','labelController@editLabel');
Route::post('cleanLabelList','labelController@cleanLabelList');
Route::get('addLabel2PrintList/{id}','labelController@addLabel2PrintList');
Route::get('removeLabelFromPrintList/{id}','labelController@removeLabelFromPrintList');
Route::get('labelList','labelController@labelList');
Route::get('editLabel/{id}','labelController@editLabel2');
Route::get('startPrint','labelController@labelList');
Route::get('addProductinLabel/{id}','labelController@addProductinLabel');

/*Print sunday total sale*/
Route::get('sunTotal','unilityController@sunTotal');

/*kill price list*/
Route::get('killprice/{id}/remove','KillPriceController@remove');
Route::get('startKillPrice','KillPriceController@startKillPrice');
Route::get('runKillPrice','KillPriceController@run');
Route::get('listAllKillProduct','KillPriceController@listAllProducts');
Route::post('startKillPrice','KillPriceController@step1');
Route::post('killpriceConfirm','KillPriceController@killpriceConfirm');
Route::post('editBottomPrice','KillPriceController@editBottomPrice');
Route::get('grabProductDetail/{id}','KillPriceController@grabProductDetail');

/*send verify email when order over $1000*/
Route::get('sendEmailOver1000/{id}','unilityController@sendReminderEmailOver1000D');

Route::get('ex_order_confirm','ExtremepcController@ex_order_detail');
Route::get('completeOrder/{order_id}','ExtremepcController@complete_order');



Route::get('wc/api','WechatController@getAccessToken');
Route::get('wc/api/addKF','WechatController@createKF');
Route::post('wc/api','WechatController@entry');

Route::get('royalpoint/run','RoyalPointController@run');
Route::get('royalpoint/givepoint/{order_id}','RoyalPointController@send_royal_point');

Route::get('run12promotion','unilityController@run12Promotion');
Route::get('run10promotion','unilityController@run10Promotion');
Route::get('runpromotion/{category_id}/{percentage}','unilityController@runPromotion');
Route::get('testeditProductPrice/{code}','unilityController@editProductPrice');
Route::group(['middleware' => ['web']], function () {
 Route::get('luckydraw','LuckyDrawController@index');
 Route::get('luckydrawlist','LuckyDrawController@clinetList');
 Route::get('luckydrawExport','LuckyDrawController@exportCsv');
 Route::get('dryPool','LuckyDrawController@dryPool');
 Route::post('luckyydraw/register','LuckyDrawController@register');
 Route::get('csv/import','CsvController@index');
 Route::post('csv/import/run','CsvController@run');
 Route::get('csv/import/{code}/start','CsvController@startImport');

});




