<?php

namespace App\Http\Controllers;

use App\Category;
use App\category_item;
use App\Category_warranty;
use App\Eta;
use App\Ex_category;
use App\Ex_customer;
use App\Ex_customer_address;
use App\Ex_manufacturer;
use App\Ex_order;
use App\Ex_order_history;
use App\Ex_product;
use App\Ex_product_attribute;
use App\Ex_product_category;
use App\Ex_product_description;
use App\Ex_product_related;
use App\Ex_product_store;
use App\Ex_speceal;
use App\Ex_stock_status;
use App\Flash_sale_products;
use App\Http\Requests;
use App\Label;
use App\News_letter;
use App\Product;
use App\adminLogin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Input;
use Mail;
use Mockery\CountValidator\Exception;
use PhpParser\Error;

class unilityController extends Controller
{
    /*
     * funtions for kill price*/

    public function killshow()
    {
        $categorys = null;
        $cats = array(
            'Mobile%20/%20Tablet%20Accessories',
            'Accessories',
            'Branded%20PC',
            'Consumables',
            'Ex-leased',
            'PC%20Parts',
            'Laptop',
            'Laptop%20Parts',
            'Mobile%20Phone',
            'Peripherals',
            'Networking',
            'Software',
            'PC%20/%20Server',
            'Tablet',
        );
        $content = array();
//        foreach ($cats as $cat) {
//            $content[$cat] = $this->getCat($cat)->total;
//        }

        return view('killprice', compact('content','categorys'));
    }

    private function getCat($cat)
    {
        $url = env('SNPORT') . "?action=ds&cat=$cat";
        $data = $this->getContent($url);
        return json_decode($data);
    }

    private function getContent($url)
    {

        // create curl resource
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, $url);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch);

        return $output;
    }

    public function killPrice(Request $request)
    {
        $categorys = null;
        $data = array();
        if ($request->has('code')) {
            $code = trim($request->input('code'));
            self::addNewProduct($code);
            $data = self::getData($code);
            $categorys = \GuzzleHttp\json_encode(self::categorysFullPath());
        }

        return view('killprice', compact('data','categorys'));
    }

    public function signProduct2Category($product_id){
        if(Input::has('id')){
            $category = Ex_category::find(Input::get('id'));
            $category->products()->attach($product_id);
            $code = Ex_product::find($product_id)->model;
            $data = self::getData($code);
            $categorys = \GuzzleHttp\json_encode(self::categorysFullPath());
            return view('killprice', compact('data','categorys'));
        }

    }

    private function getData($code)
    {
        $url = env('SNPORT') . "?action=test&code=$code";
        $pricedetail = $this->getContent($url);
        $url = env("SNPORT") . "?action=c&code=$code";
        $des = self::getContent($url);
        $product = Ex_product::where('model', $code)->first();
        $viewed = $product->viewed;
        $product_id = $product->product_id;
        $special = 0;
        $status = 0;
        if (isset($product->price)) {

            $extremepc = $product->price * 1.15;
            $extremepc = round($extremepc, 2);


            $special = Ex_speceal::where('product_id', $product->product_id)->first();
            $special_start = isset($special->date_start)?$special->date_start:'';
            $special_end = isset($special->date_end)?$special->date_end:'';
            if (isset($special->price)) {
                if ($special->date_end <> '0000-00-00') {
                    $enddate = Carbon::parse($special->date_end);
                    $startdate = Carbon::parse($special->date_start);
                    $now = Carbon::now();
                    if ($now->between($startdate, $enddate)) {
                        $special = $special->price * 1.15;
                    } else {
                        $special = 0;
                    }

                } else {
                    $special = $special->price * 1.15;
                }
            }


//            $special = isset($special->price) ? $special->price * 1.15 : 0;

            $status = $product->status;

        } else {

            $extremepc = "Cannot find the product";
        }

        $url = env("SNPORT") . "?action=sc&code=$code";
        $supplier_code = self::getContent($url);
        $averageCost = 0;
        if(str_contains($pricedetail,'Average price inc')){
            $productDetailArray = explode('<br>',$pricedetail);
            $averageCost = str_replace('<font color=red>Manual price inc: $','',$productDetailArray[6]);
            $averageCost = str_replace('</font>','',$averageCost);
            $averageCost = trim($averageCost);
        }
        $data = array(
            'code' => $code,
            'price' => $pricedetail,
            'special' => round($special, 2),
            'des' => $des,
            'extremepcprice' => $extremepc,
            'supplier_code' => $supplier_code,
            'status' => $status,
            'view'=>$viewed,
            'product_id'=>$product_id,
            'special_start'=>$special_start,
            'special_end'=>$special_end,
            'img'=>$product->image,
            'bottom_cost'=>$averageCost
        );
        return $data;
    }

    /*kill price functions end*/

    /*=====================================================================================================================*/

    /*price sync with pricespy
    unfinish*/

    /*
     * get category json data
     * param: cat = category, string*/

    public function killPrice_edit(Request $request)
    {

        if ($request->has('code')) {
            $product = Ex_product::where('model', $request->input('code'))->first();
            $product->price = $request->price / 1.15;
            $product->save();

                Ex_speceal::where('product_id', $product->product_id)->delete();
            if (empty($request->input('special') * 1.0)) {
                if(!empty($product->jan)){
                    $product->stock_status_id = $product->jan;
                    $product->jan = '';
                    $product->save();
                }
            } else {
                $special = new Ex_speceal();
                $special->product_id = $product->product_id;
                $special->customer_group_id = 1;
                $special->priority = 0;
                $special->price = $request->input('special') / 1.15;
                $special->date_start = "0000-00-00";
                if(!empty($request->input('starttime'))){
                    $special->date_start = Carbon::parse($request->input('starttime'))->format('Y-m-d');
                }
                    $special->date_end = "0000-00-00";
                if(!empty($request->input('endtime'))){
                    $special->date_end = Carbon::parse($request->input('endtime'))->format('Y-m-d');
                    $product->jan = $product->stock_status_id;
                    $product->stock_status_id = 31;
                    $product->save();
                }
                $special->save();

            }

            if ($request->has('product_status')) {
                if ($request->input('product_status') == 'Disable') {
                    $product->status = 1;
                } else {
                    $product->status = 0;
                }
                $product->save();
            }

            $categorys = null;
            $categorys = \GuzzleHttp\json_encode(self::categorysFullPath());
            $data = self::getData($request->input('code'));
            return view('killprice', compact('data','categorys'));


        }
    }

    /*price sync pricespy end*/


    /*=====================================================================================================================*/


    /*
     * warranty guide functions
     * */

    public function addPricespyMap(Request $request)
    {
        if ($request->has('pricespy_url')) {
            $product = new Product();
            $product->code = $request->input('code');
            $product->description = $request->input('description');
            $product->pricespy_url = $request->input('pricespy_url');

        }
    }

    public function showWarrantyGuide()
    {
        $categorys = Category::all();
        return view('warrantyguide', compact('categorys'));
    }

    public function warrantySubCategory($id)
    {
        $category = Category::find($id)->name;
        $category_items = category_item::where('category_id', $id)->get();
        $suppliers = array();

        foreach ($category_items as $item) {
            $suppliers[] = Category_warranty::find($item->warranty_detail_id);
        }

        return view('warranty_subcategory', compact('category', 'suppliers'));
    }

    /*
     * warranty guide functions end*/

    /*=====================================================================================================================*/


    /*
     * pricespy product feed*/

    public function warrantydetail($id)
    {
        $supplier = Category_warranty::find($id);
        return view('warrantydetail', compact('supplier'));
    }
    /*
     * pricespy product feed end*/


    /*=====================================================================================================================*/

    /*sync data from roctech to extremepc functions*/
    /*
     * sync the quantity from roctech to extremepc
     * */

    public function productFeed()
    {
        try {


            $products = Ex_product::all();
            $feed = array();
            foreach ($products as $product) {
                $stock_status = 'Yes';
                $special = Ex_speceal::where('product_id', $product->product_id)->first();
                $product_name = isset(Ex_product_description::find($product->product_id)->name) ? Ex_product_description::find($product->product_id)->name : '';

                if ($product->quantity <= 0) {
                    if ($product->stock_status_id == 5) {
                        $stock_status = 'No';
                    } else {
                        $stock_status = 'Incoming';
                    }
                }
                $categorys = null;
                $categorys = $product->categorys;

                $categorytree = null;
                $categorytree = "";
                if (count($categorys) > 0) {
                    foreach ($categorys as $category) {
                        $desc = $category->description;
                        $categorytree .= $desc->name;
                        $categorytree .= "/";
                    }
                }

//           echo  htmlspecialchars_decode($categorytree);


                $tem = array(
                    'Product name' => $product_name,
                    'Article number' => $product->model,
                    'Manufacturer' => $product->manufacturer_id == 0 ? 'null' : Ex_manufacturer::find($product->manufacturer_id)->name,
                    'URL to the product page' => "http://www.extremepc.co.nz/index.php?route=product/product&product_id=$product->product_id",
                    'Product category' => $categorytree,
                    'Price' => round($product->price * 1.15, 2),
                    'Status' => 'Normal',
                    'Stock status' => $stock_status


                );
                if (isset($special->date_end)) {
                    if ($special->date_end <> '0000-00-00') {
                        $enddate = Carbon::parse($special->date_end);
                        $startdate = Carbon::parse($special->date_start);
                        $now = Carbon::now();
                        if ($now->between($startdate, $enddate)) {
                            $tem['Price'] = round($special->price * 1.15, 2);
                        }

                    } else {
                        $tem['Price'] = round($special->price * 1.15, 2);
                    }
                }

                $feed[$product->product_id] = $tem;


            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        echo \GuzzleHttp\json_encode($feed);
    }

    /*
     * TSA Product Feed*/

    public function tsaFeed()
    {
        try {

            $url = $url = env('SNPORT') . "?action=tsa";

            $content = self::getContent($url);

            $content = str_replace(',}', '}', $content);
            $content = \GuzzleHttp\json_decode($content, true);



            $products = Ex_product::where('status',1)->get();
            $feed = array();
            foreach ($products as $product) {
                if($product->manufacturer_id == 23 || !isset($content[$product->model])){
                    continue;
                }

                $stock_status = 'Yes';
                $special = Ex_speceal::where('product_id', $product->product_id)->first();
                $product_description = Ex_product_description::find($product->product_id);
                $product_name = isset($product_description->name) ? $product_description->name : '';
                $product_desc = isset($product_description->description) ? $product_description->description : '';
                $product_quantity = $product->quantity;

                if ($product->quantity <= 0) {
                    if ($product->stock_status_id == 5) {
                        $stock_status = 'No';
                    } else {
                        $stock_status = 'Incoming';
                    }
                }
                $categorys = null;
                $categorys = $product->categorys;

                $categorytree = null;
                $categorytree = "";
                if (count($categorys) > 0) {
                    foreach ($categorys as $category) {
                        $desc = $category->description;
                        $categorytree .= $desc->name;
                        $categorytree .= ",";
                    }
                }

                $images = $product->images;

                $image_array = array();

                $image_array[] = 'http://www.extremepc.co.nz/image/'.$product->image;

                foreach($images as $image){
                    $image_array[] = 'http://www.extremepc.co.nz/image/'.$image->image;
                }

//           echo  htmlspecialchars_decode($categorytree);


                $tem = array(
                    'Product name' => $product_name,
                    'Product description' => addslashes($product_desc),
                    'Quantity' => $product_quantity,
                    'Article number' => $product->model,
                    'Manufacturer' => $product->manufacturer_id == 0 ? 'null' : Ex_manufacturer::find($product->manufacturer_id)->name,
                    'URL to the product page' => "http://www.extremepc.co.nz/index.php?route=product/product&product_id=$product->product_id",
                    'Product category' => $categorytree,
                    'Price' => round($content[$product->model] * 1.05, 2),
                    'Stock status' => $stock_status,
                    'Images' => $image_array


                );


                $feed[$product->product_id] = $tem;


            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        echo \GuzzleHttp\json_encode($feed);
    }

    public function ktechProductFeed(){
        try {


            $products = Ex_product::where('quantity','<>',0)->get();
            $feed = array();
            foreach ($products as $product) {

                $product_name = isset(Ex_product_description::find($product->product_id)->name) ? Ex_product_description::find($product->product_id)->name : '';



                $tem = array(
                    'Product name' => addslashes($product_name),
                    'Article number' => $product->model,
                    'quantity'=>$product->quantity
                );


                $feed[$product->product_id] = $tem;


            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        echo \GuzzleHttp\json_encode($feed);
    }
    /*
     * batch change order status*/
    public function changeOrderStatus()
    {
//        $orders = Ex_order::where('order_status_id', 21)->get();
        $orders = Ex_order::where('order_status_id', 21)->where('date_added', '<', '2016-10-16')->get();
        $list = array();
        foreach ($orders as $order) {
            $history = new Ex_order_history();
            $history->order_id = $order->order_id;
            $history->order_status_id = 5;
            $history->notify = 0;
            $history->comment = '';
            $history->date_added = Carbon::now();
            $history->save();
            $order->order_status_id = 5;
            $order->date_modified = Carbon::now();
            $order->save();
            $list[$order->order_id] = $order;
        }

    }

    public function insert_laptop_attribute(Request $request){
//        dd($request);
        for($i = 30; $i <= 38; $i++){

            if(count(Ex_product_attribute::where('product_id',$request->input('product_id'))->where('attribute_id',$i)->get())>0){
              Ex_product_attribute::where('product_id',$request->input('product_id'))->where('attribute_id',$i)->delete();

            }
            if(empty($request->input($i))) continue;
                $attribute = new Ex_product_attribute();
                $attribute->product_id = $request->input('product_id');
                $attribute->attribute_id = $i;
                $attribute->text = $request->input($i);
                $attribute->language_id = 1;
                $attribute->save();



        }
        $code = Ex_product::find($request->input('product_id'))->model;
        $data = self::getData($code);
        $categorys = null;
        $categorys = \GuzzleHttp\json_encode(self::categorysFullPath());
        return view('killprice', compact('data','categorys'));
    }

    public function adminLogin(Request $request){

        if(count(adminLogin::where('username',$request->input('username'))->where('password',md5($request->input('password')))->get())>0){

            $admin = adminLogin::where('username',$request->input('username'))->where('password',md5($request->input('password')))->first();
            $admin->ip = self::getIP();
            $admin->save();

        }
        return redirect('list');
    }
    private function getIP()
    {
        if (getenv("HTTP_CLIENT_IP")) {
            $ip = getenv("HTTP_CLIENT_IP");
        } elseif (getenv("HTTP_X_FORWARDED_FOR")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } elseif (getenv("REMOTE_ADDR")) {
            $ip = getenv("REMOTE_ADDR");
        } else {
            $ip = "unknow";
        }
        return $ip;
    }
    /*
     * sign laptop attribute*/
    public function laptop_attribute($id){
        $graphics_card = array(
            "GT720M"=>"GT720M",
            "GTX860M"=>"GTX860M",
            "GTX920M"=>"GTX920M",
            "GTX930M"=>"GTX930M",
            "GTX940M"=>"GTX940M",
            "GTX950M"=>"GTX950M",
            "GTX960M"=>"GTX960M",
            "GTX965M"=>"GTX965M",
            "GTX970M"=>"GTX970M",
            "GTX980M"=>"GTX980M",
            "GTX1060"=>"GTX1060",
            "GTX1070"=>"GTX1070",
            "GTX1080"=>"GTX1080",
            "Radeon R5 M230"=>"Radeon R5 M230",
            "Integrated"=>"Integrated"
        );
        $resolution = array(
            "1366 X 768"=>"1366 X 768",
            "1440 X 900"=>"1440 X 900",
            "1600 X 900"=>"1600 X 900",
            "1920 X 1080"=>"1920 X 1080",
            "1920 X 1200"=>"1920 X 1200",
            "2304 X 1440"=>"2304 X 1440",
            "2560 X 1440"=>"2560 X 1440",
            "2560 X 1600"=>"2560 X 1600",
            "2736 X 1824"=>"2736 X 1824",
            "2880 X 1620"=>"2880 X 1620",
            "3000 X 2000"=>"3000 X 2000",
            "3200 X 1800"=>"3200 X 1800",
            "3840 X 2160"=>"3840 X 2160"
        );

        $cpus = array(
            "Intel Atom"=>'Intel Atom',
            "Intel Celeron"=>'Intel Celeron',
            "Intel Core i3"=>'Intel Core i3',
            "Intel Core i5"=>'Intel Core i5',
            "Intel Core i7"=>'Intel Core i7',
            "Intel Core M"=>'Intel Core M',
            "Intel Pentium"=>'Intel Pentium',
            "Intel Xeon E3"=>'Intel Xeon E3',
            "AMD A4"=>'AMD A4',
            "AMD A6"=>'AMD A6',
            "AMD A8"=>'AMD A8',
            "AMD A10"=>'AMD A10',
            "AMD E1"=>'AMD E1'
        );
        $os = array(
            'Windows 10 home'=>'Windows 10 home',
            'Windows 10 pro'=>'Windows 10 pro',
            'Windows 8 home'=>'Windows 8 home',
            'Windows 8 pro'=>'Windows 8 pro',
            'Windows 8.1'=>'Windows 8.1',
            'Windows 7 home'=>'Windows 7 home',
            'Windows 7 pro'=>'Windows 7 pro',
            'Chrome Os'=>'Chrome Os',
            'Mac Os'=>'Mac Os',
        );
        $data = array();
        for($i = 30; $i <= 38; $i++){
            $attribute = Ex_product_attribute::where('product_id',$id)->where('attribute_id',$i)->first();
            $data[$i] = empty($attribute)?null:$attribute->text;
        }
        $product = Ex_product_description::where('product_id',$id)->first();
        return view('laptop',compact('cpus','resolution','graphics_card','id','product','data','os'));
    }

    /*
     * grab sync qty arrary*/

    public function sync(Request $request)
    {
        $match = 0;
        $quantity = 0;
        $disable = 0;
        $save = 0;
        if ($request->has('quantity')) {
            $quantity = 1;
        }
        if ($request->has('disable')) {
            $disable = 1;
        }
        if ($request->has('match')) {
            $match = 1;
        }
        if ($quantity == 1 || $disable == 1) {
            $save = 1;
        }
//        if($request->has('self')){
//            $self = 1;
//        }
        $status = $request->input('status');
        if ($status == '2') {
            $products = Ex_product::where('status', 1)->get();
        } else {
            $products = Ex_product::all();
        }

        $total = count($products);
        $int = 0;
        $unfound = array();
        foreach ($products as $product) {
            $code = $product->model;
            $url = env('SNPORT') . "?action=sync&code=$code";
            $quantity = $this->getContent($url);
            $pos = strpos($quantity, 'Error');
            if ($pos === false) {
                if ($quantity == 1) {
                    $product->quantity = $quantity;
                }


            } else {
                if ($disable == 1) {
                    $product->status = 0;
                }
                $int++;
                $unfound[] = $code;
            }
            if ($save == 1) {
                $product->save();
            }
        }
        $result = array(
            'total' => $total,
            'int' => $int,
            'unfound' => $unfound
        );
        return view('sync', compact('result'));
    }
    /* grab sync qty array end*/
    /*daily sync quantity*/

    public function dailySync()
    {
        self::checkOrder();
        self::categoryarrange();
//        self::listnewclient();
        self::specialCheck();
        self::selfClearSpecial();
//        self::producttosales();
//        self::categoryarrange();
        return self::syncQuantity(); //sync quantity
    }
    private function specialCheck(){
        $specials = Ex_speceal::all();
        foreach($specials as $item){
            if($item->date_end!='0000-00-00'){
                $date_end = Carbon::parse($item->date_end);
                $diff = $date_end->diffInDays(Carbon::now());
                if($diff<0){
                    $product = $item->product;
                    if(!empty($product->jan)){
                        $product->stock_status_id = $product->jan;
                        $product->jan = '';
                        $product->save();
                    }
                    $item->delete();
                }

            }
        }

    }
    public function producttosales(){
        Ex_product_category::where('category_id',272)->delete();
        $products = Ex_speceal::where('date_end','>',Carbon::now())->orwhere('date_end','0000-00-00')->get();
        $num = count($products);
        $keys = array();
        if($num > 18){
            while(count($keys)<18){
                $key = random_int(0,$num-1);
                if(!in_array($key,$keys))
                    $keys[] = $key;
            }
        }else{
            for($i = 0;$i < $num;$i++){
                $keys[] = $i;
            }
        }

        foreach($keys as $value){
            $product = Ex_product::find($products[$value]->product_id);
            if($product->quantity>0){
                $category = new Ex_product_category();
                $category->product_id = $products[$value]->product_id;
                $category->category_id = 272;
                $category->save();
            }

        }
    }

    public function checkOrder()
    {
        $orders = Ex_order::all();
        $reminderStatus = array(
            19, 17
        );
        $urgentlist = array();
        foreach ($orders as $order) {

            $status = $order->order_status_id;

            if (in_array($status, $reminderStatus)) {
                $date = Carbon::parse($order->date_modified);
                $date = $date->dayOfYear + 2;

                if ($date <= (Carbon::now()->dayOfYear)) {
                    $tem = array(
                        0 => $order,
                        1 => $order->items,
                        2 => $status == 19 ? 'Back Order' : 'Payment Check'
                    );
                    $urgentlist[] = $tem;
                }

                if($status == 17){
                    $add_date = Carbon::parse($order->date_added);
                    $diffDay = $add_date->diffInDays(Carbon::now());
                    if($diffDay == 4 || $diffDay == 10){
                        self::sendPaymentReminder($order);
                    }
                    if($diffDay == 30){
                        $history = new Ex_order_history();
                        $history->order_id = $order->order_id;
                        $history->order_status_id = 7;
                        $history->notify = 0;
                        $history->comment = '';
                        $history->date_added = Carbon::now();
                        $history->save();
                        $order->order_status_id = 7;
                        $order->date_modified = Carbon::now();
                        $order->save();

                    }
                }


            }

        }
        if (count($urgentlist) > 0) {
            Mail::send('reminder', compact('urgentlist'), function ($m) {
                $m->from('no-reply@extremepc.co.nz', 'Extremepc Reminder');
                $m->bcc('tony@roctech.co.nz', 'Tony Situ');
                $m->bcc('hugo@roctech.co.nz', 'Hugo Wang');
                $m->to('sales@roctech.co.nz', 'Roctech')->subject('Online Order Reminder!');
            });
        }

    }



    public function sendPaymentReminder($order){

//        $order = Ex_order::find($code);
//        dd($order);
        Mail::send('email.paymentreminder', compact('order'), function ($m) use ($order){
            $m->from('no-reply@extremepc.co.nz', 'Extremepc Payment Reminder');
            $m->bcc('tony@roctech.co.nz', 'Tony Situ');
            $m->bcc('hugo@roctech.co.nz', 'Hugo Wang');
            $email = $order->email;
            $name = $order->firstname.' '.$order->lastname;
            $m->to($email,$name)->subject('ExtremePC Online Order Reminder!');
        });
    }

    public function syncQuantity()
    {
        $products = Ex_product::all();
        $roctech_array = self::syncqty();
        $unsync = array();
        $disable = array();
//        dd($roctech_array);
        foreach ($products as $product) {
            if (isset($roctech_array[$product->model])) {
//                dd($roctech_array[$product->model]);
                if ($roctech_array[$product->model][0] == 'True') {
                    $product->status = 0;
                    $disable[] = $product->model;
                } else {
                    $product->quantity = $roctech_array[$product->model][1];
                    $product->status = 1;
                }
                $product->save();
            } else {
                $unsync[] = $product->model;
            }

        }

        self::checkEta($roctech_array);

        $total_enable = count(Ex_product::where('status', 1)->get());
        $total_disable = count(Ex_product::where('status', 0)->get());

        $content = 'Last sync is at' . date(' jS \of F Y h:i:s A');
        return view('self_sync', compact('content', 'unsync', 'disable', 'total_enable', 'total_disable'));
    }

    private function checkEta($products){
        $etas = Eta::all();

        foreach($etas as $eta){
            if(isset($products[$eta->model])){
                if($products[$eta->model][1]>0){
                    self::eta_remove($eta->id);
                    continue;
                }
            }


            $date = Carbon::parse($eta->available_time);
            if($date->lte(Carbon::now())){

                $date = $date->addWeek(2)->format('d-m-Y');

                $name = 'Pre-Order<span>Releases:</span> '.$date;
                $stock_status = Ex_stock_status::where('name','like',"%$name%")->first();
                if(empty($stock_status->name)){
                    $stock_status = new Ex_stock_status();
                    $stock_status->language_id=1;
                    $stock_status->name = $name;
                    $stock_status->save();
                }

                $products = Ex_product::where('model',$eta->model)->get();
                if(count($products)>0){


                    foreach($products as $product){
                        $product->stock_status_id = $stock_status->stock_status_id;
                        $product->save();
                    }


                    $eta->available_time = $date;
                    $eta->save();

                    Mail::raw($eta->model.' eta over due', function ($m) {
                        $m->from('no-reply@extremepc.co.nz', 'Extremepc Reminder');
                        $m->bcc('tony@roctech.co.nz', 'Tony Situ');
                        $m->bcc('hugo@roctech.co.nz', 'Hugo Wang');
                        $m->to('sales@roctech.co.nz', 'Roctech')->subject('ETA Reminder!');
                    });


                }

            }


        }
    }

    public function syncqty()
    {
        $url = env('SNPORT') . "?action=allqty";
        $content = self::getContent($url);
//        $content = str_replace(':,', ':0,', $content);
        $content = str_replace(',}', '}', $content);
        $content = \GuzzleHttp\json_decode($content, true);

        return $content;
    }

    public function createRoctechOrder($id)
    {
        $clientid = self::addNewClient($id);
        if (trim($clientid) == 'Error') {
            $clientid = 0;
        }

        $roctech_order_id = self::addOrder($id, $clientid);

        if (trim($roctech_order_id) == 'Error') {
            echo 'Error';
            return false;
        }
        self::insertOrderItem($id, $roctech_order_id);
        return redirect("http://192.168.1.3/admin/olist.aspx?r=&id=$roctech_order_id");


    }

    public function addNewClient($id)
    {
        $url = env('SNPORT') . "?action=newclient";

        $order = Ex_order::find($id);
        $name = $order->firstname . ' ' . $order->lastname;
        $name = str_replace('\'','\'\'',$name);
        $email = $order->email;
        $phone = $order->telephone;
        $company = str_replace('\'','\'\'',$order->shipping_company);
        $address1 = str_replace('\'','\'\'',$order->shipping_address_1);
        $address2 = str_replace('\'','\'\'',$order->shipping_address_2);
        $city = $order->shipping_city;
        $province = $order->shipping_zone;
        $data = compact('name', 'email', 'phone', 'company', 'address1', 'address2', 'city', 'province');
        return self::sendData($url, $data);
    }

    private function sendData($url, $data)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);



        return $server_output;

    }


    public function addOrder($id, $clientId)
    {
        $url = env('SNPORT') . "?action=createorder";

        $order = Ex_order::find($id);


        $phone = $order->telephone;
        $company = isset($order->shipping_company)?addslashes($order->shipping_company):" ";
        $address1 = addslashes($order->shipping_address_1);
        $address2 = addslashes($order->shipping_address_2);
        $city = addslashes($order->shipping_city) . ' ' . addslashes($order->shipping_zone);
        $orderid = '#' . $id;
        $comment = str_replace("'","^",$order->comment);
        $ship_status = $order->shipping_method == 'Free Shipping' ? 1 : 0;
        $ship_fee = $order->shipfee();
        $ship_postcode = $order->shipping_postcode;
        $ship_name = addslashes($order->shipping_firstname.' '.$order->shipping_lastname);
        $data = compact('phone', 'company', 'address1', 'address2', 'city', 'orderid', 'ship_status', 'clientId', 'comment','ship_fee','ship_postcode','ship_name');
//        dd($data);
        return self::sendData($url, $data);
    }



    public function insertOrderItem($id, $roctech_id)
    {
        $url = env('SNPORT') . "?action=orderitem";

        $order = Ex_order::find($id);
        $order_id = $roctech_id;
        $items = $order->items;
        foreach ($items as $item) {
            $model = $item->model;
            $quantity = $item->quantity;
            $name = addslashes($item->name);
            $price_ex = $item->price;
            $data = compact('order_id', 'model', 'quantity', 'name', 'price_ex', 'data');
            self::sendData($url, $data);
        }
    }

    public function showSync()
    {
        return view('sync');
    }

    public function self_check()
    {
        $products = Ex_product::where('status', 1)->get();
        $total = count($products);
        $int = 0;
        $content = '';
        foreach ($products as $product) {

            $code = $product->model;
            $url = env('SNPORT') . "?action=sync&code=$code";
            $quantity = $this->getContent($url);
            $pos = strpos($quantity, 'Error');
            if (!$pos === false) {
                $int++;

                $content .= $code;
                $content .= '<br>';
            }

        }
        $percentage = $int * 1.0 / $total;
        $percentage = round($percentage, 2) * 100;
        return view('sync', compact('content', 'percentage'));
    }

    public function grabProducts()
    {
        $url = env('SNPORT') . "?action=products";
        $content = self::getContent($url);
        $content = str_replace(',]', ']', $content);
        $codes = \GuzzleHttp\json_decode($content);
        foreach ($codes as $code) {
            echo self::addNewProduct($code);
            echo '<br>';

        }

    }

    public function addNewProduct($code)
    {
        if (self::checkCodeEx($code)) {
            echo $code . ' <font color="red">code exist</font>';
        } else {
            $url = env('SNPORT') . "?action=prosync&code=$code";
            try{
                $data = self::getContent($url);
                    for ($i = 0; $i <= 31; ++$i) {
                        $data = str_replace(chr($i), "", $data);
                    }
                $data = str_replace(chr(127), "", $data);

// This is the most common part
// Some file begins with 'efbbbf' to mark the beginning of the file. (binary level)
// here we detect it and we remove it, basically it's the first 3 characters
if (0 === strpos(bin2hex($data), 'efbbbf')) {
    $data = substr($data, 3);
}
                $data = \GuzzleHttp\json_decode($data);

            }catch (\League\Flysystem\Exception $e){
                echo $e->getMessage();
                exit;

            }

            if (!empty(trim($data->name))) {


                $spec = $data->spec;
                $data->spec = str_replace('{!@!}', '"', $spec);

                $tem = array(
                    'model' => $data->code,
                    'quantity' => 0,
                    'stock_status_id' => 9,
                    'shipping' => 1,
                    'price' => $data->price,
                    'tax_class_id' => 9,
                    'weight' => $data->weight,
                    'weight_class_id' => 1,
                    'subtract' => 1,
                    'sort_order' => 1,
                    'status' => 1,
                    'date_added' => Carbon::now()


                );
                $product = Ex_product::create($tem);
                //        dd($product);
                self::imageCopy($data->code);
                $product->image = 'catalog/autoEx/' . $data->code . '.jpg';
                $product->save();
                $store = new Ex_product_store();
                $store->product_id = $product->product_id;
                $store->store_id = 0;
                $store->save();
                $description = New Ex_product_description();
                $description->product_id = $product->product_id;
                $description->language_id = 1;
                $description->name = htmlspecialchars(str_replace('{!@!}', '"', $data->name));
                $description->description = str_replace('{!@!}', '"', $data->spec);
                $description->meta_title = $data->name;
                $description->save();
//                $category = new Ex_product_category();
//                $category->product_id = $product->product_id;
//                $category->category_id = 267;
//                $category->save();
                $label = Label::where('code',$product->model)->first();
                if(is_null($label)){
                    $label = new Label();
                    $label->code = $product->model;
                    $label->description = $description->name;
                    $label->price = round($product->price*1.15,2);
                    $label->prepare2print = 1;
                    $label->save();
                }


                return $product->model . ' <font color="green">Insert Sucessed</font>';
            } else {
                return $data->model . ' <font color="red">No Name</font>';
            }
        }


    }

    private function checkCodeEx($code)
    {
        if (count(Ex_product::where('model', $code)->get()) > 0) {
            return true;
        } else {
            return false;
        }
    }
    /*
     * sync data from roctech to extremepc functions end */

    /*=====================================================================================================================*/

    /*
    news_letter one off job to transfer data from old roctech data
    */
//    public function old_transfer()
//    {
//
//        $clients = Old_client::All();
//
//        foreach ($clients as $client) {
//            if (!empty($client->customers_email_address)) {
//                $news_letter = new News_letter();
//
//                $news_letter->firstname = $client->customers_firstname;
//                $news_letter->lastname = $client->customers_lastname;
//                $news_letter->email = $client->customers_email_address;
//                $news_letter->save();
//            }
//        }
//    }


    /*=====================================================================================================================*/

    /*
     * news letter functions
     * */

    private function imageCopy($code)
    {
        $url = env('IMGREMOTE') . $code . '.jpg';
        if (self::imageExist($url)) {
            copy($url, "/var/www/extremepc.co.nz/public_html/image/catalog/autoEx/$code.jpg");
        }
    }

    private function imageExist($url)
    {

        $file_headers = @get_headers($url);
        if ($file_headers[0] == 'HTTP/1.1 404 Not Found') {
            $exists = false;
        } else {
            $exists = true;
        }
        return $exists;
    }

    /*
     * news letter functions end*/


    /*=====================================================================================================================*/

    /*
     * Common functions*/

    public function relatedproduct()
    {
        $category = Ex_category::find(8);

        $products = $category->products;

        $productidgroup = array();

        foreach ($products as $product) {

            if ($product->status <> 0) {
                $productidgroup[] = $product->product_id;
            }
        }


        $relatedProduct = array(439, 1177, 1844, 363);

        foreach ($relatedProduct as $item) {
            foreach ($productidgroup as $id) {
                if (count(Ex_product_related::where('product_id', $id)->where('related_id', $item)->get()) > 0) {
                    continue;
                } else {
                    $product_related = new Ex_product_related();
                    $product_related->product_id = $id;
                    $product_related->related_id = $item;
                    $product_related->save();
                }
            }
        }


    }

    public function addtoaoc(){
        $products = Ex_product::where('manufacturer_id',63)->get();
        foreach($products as $product){
            $product_category = new Ex_product_category();
            $product_category->category_id = 278;
            $product_category->product_id = $product->product_id;
            $product_category->save();
        }
    }
    public function sales_list(){

        $categotys = [
            321,322,323
        ];
        $category_names = array();
        $category_products = array();
        foreach ($categotys as $id){
            $category = Ex_category::find($id);
            $products = $category->products();
            $category_products[$id] = $products;
            $category_names[$id] = $category->description->name;
        }
//        $sales = Ex_product_category::where('category_id',272)->get();
//        $result = array();
//        foreach($sales as $sale){
//            $product = Ex_product::find($sale->product_id);
//            $product_detail = Ex_product_description::where('product_id',$sale->product_id)->first();
//            $result[] = compact('product','product_detail');
//        }
//        dd($result);
//        return view('sales_list',compact('result'));
        return view('sales_list',compact('category_names','categotys','category_products'));

    }
    public function sales_add(Request $request){
        $this->validate($request,[
            'code'=>'required'
        ]);
        $product = Ex_product::where('model',trim($request->input('code')))->first();
        if(!is_null($product)){
            $category = Ex_category::find($request->input('category_id'));
            $category->products()->attach($product->id);
        }
//        foreach($models as $model){
//            if(empty($model))
//                continue;
//            $product = Ex_product::where('model',$model)->first();
//            if(isset($product)){
//
//
//
//                $category_product  = new Ex_product_category();
//                $category_product->category_id = 272;
//                $category_product->product_id = $product->product_id;
//                $category_product->save();
//
//
//
//            }else{
//                throwException('Can find model');
//            }
//        }
//        return redirect('sales_list');

    }
    public function sales_remove($id){

        $category = Ex_product_category::where('category_id',272)->where('product_id',$id)->delete();
        return redirect('sales_list');

    }

    public function eta_list(){
        $etas = Eta::all();
        return view('eta_list',compact('etas'));
    }
    public function listnewclient(){
        $adddate = Carbon::now()->day(-1)->format('Y-m-d');
        $clients = Ex_customer::where('date_added','like',$adddate.'%')->get();
        Mail::send('email.newClientReminder', compact('clients'), function ($m) {
            $m->from('no-reply@extremepc.co.nz', 'Extremepc Reminder');

//            $m->bcc('hugo@roctech.co.nz', 'Hugo Wang');

            $m->to('stmssky@hotmail.com', 'Tony Situ')->subject('Extremepc Reminder!');

        });

    }

    public function eta_add(Request $request){
//        dd($request);
        $models = $request->input('modelnum');
//        dd($models);
        $date = Carbon::parse($request->input('available_time'));
        $date =  $date->format('d-m-Y');

        $name = 'Pre-Order<span>Releases:</span> '.$date;
        $stock_status = Ex_stock_status::where('name','like',"%$name%")->first();
        if(empty($stock_status->name)){
            $stock_status = new Ex_stock_status();
            $stock_status->language_id=1;
            $stock_status->name = $name;
            $stock_status->save();
        }
        foreach($models as $model){
            if(empty($model))
                continue;
            $products = Ex_product::where('model',$model)->get();
            if(count($products)>0){


                foreach($products as $product){
                    $product->stock_status_id = $stock_status->stock_status_id;
                    $product->save();
                }
//                Eta::create($request->all());
                $eta = new Eta();
                $eta->model = $model;
                $eta->available_time = $date;
                $eta->save();


            }else{
                throwException('Can find model');
            }
        }
        return redirect('eta_list');



    }
    public function eta_remove($id){
        $eta = Eta::find($id);
        $products = Ex_product::where('model',$eta->model)->get();
        foreach($products as $product){
            $product->stock_status_id = 9;
            $product->save();
        }
        $name = 'Pre-Order<span>Releases:</span> '.$eta->available_time;
        $stock_status = Ex_stock_status::where('name',$name)->first();
        if(count($stock_status->products)<1){
            $stock_status->delete();
        }

        Eta::destroy($id);
        return redirect('eta_list');

    }
    public function categoryarrange()
    {
        $products = Ex_product::where('status', 1)->get();
        $uncategory = array();
        foreach ($products as $product) {
            $categorys = $product->categorys;
            if (count($categorys) > 0) {
                foreach ($categorys as $category) {
                    $insert = 0;
                    $parent = $category->parentCategory();
                    while (!empty($parent)) {
                        foreach ($categorys as $other) {
                            if ($parent->equal($other)) {
                                $insert = 1;
                                break;
                            }
                        }
                        if ($insert == 0) {
                            if(count(Ex_product_category::where('product_id',$product->product_id)->where('category_id',$parent->category_id)->get())<1){
                                $product_category = new Ex_product_category();
                                $product_category->product_id = $product->product_id;
                                $product_category->category_id = $parent->category_id;
                                $product_category->save();
                            }

                        } else {
                            $insert = 0;
                        }

                        $parent = $parent->parentCategory();
                    }


                }
            } else {
                $uncategory[] = $product->product_id;
            }
        }
//        var_dump($uncategory);
    }

    public function showAucklandCustomer()
    {
        $customers = Ex_customer_address::groupBy('customer_id')->get();
        echo count($customers) . '<br>';
        echo '<table style="border: solid 1px">';
        echo '<tr><th>Name</th><th>Address</th><th>Price</th><tr>';
        foreach ($customers as $customer) {


            if (count(Ex_order::where('customer_id', $customer->customer_id)->get()) > 0) {
                echo '<tr><td>'.$customer->firstname . ' ' . $customer->lastname . ' </td><td> ' . $customer->address_1 . ' ' . $customer->address_2.'</td>';

                echo ' <td>'.Ex_order::where('customer_id', $customer->customer_id)->sum('total').'</td><tr>';
            }
        }
        echo '</table>';
    }

    public function sendNewsLetter()
    {
        $newsletters = News_letter::All();

        //$newsletters = News_letter::where('email','hugowangchn@gmail.com')->get();
        foreach ($newsletters as $user) {
            Mail::send('newsletter', compact('user'), function ($m) use ($user) {
                $m->from('no-reply@extremepc.co.nz', 'Extreme PC');
                $m->replyTo('sales@roctech.co.nz', 'Sales Department');
                $m->to($user->email)->subject('New Website Launch Deal Extreme PC');
            });

        }
    }

    public function unsubscribe($email)
    {
        $unsubscribe = News_letter::where('email', $email)->first();
        if (!empty($unsubscribe->email)) {

            $unsubscribe->status = 'false';
            $unsubscribe->save();
        }
        echo 'Unsubscribe successful! Thanks';
    }

    private function save2Extremepc($data)
    {
        $product = new Ex_product();
        foreach ($data as $key => $value) {
            $product->$key = $value;
        }
        $product->save();
        return $product;
    }

    private function save2Description($data)
    {
        $description = new Ex_product_description();
        foreach ($data as $key => $value) {
            $description->$key = $value;
        }
        $description->save();
        return $description;
    }

    private function dataFactory($type, $data)
    {
        $variableGroup = array(
            'product' => array('model', 'sku', 'upc', 'ean', 'jan', 'isbn',
                'mpn', 'location', 'quantity', 'stock_status_id', 'image', 'manufacturer_id',
                'shipping', 'price', 'points', 'tax_class_id', 'date_available', 'weight',
                'weight_class_id', 'length', 'width', 'height', 'length_class_id', 'subtract',
                'minimum', 'sort_order', 'status', 'viewed', 'date_added', 'date_modified'),
            'description' => array('product_id', 'language_id', 'name', 'description', 'tag',
                'meta_title', 'meta_description', 'meta_keyword'),
        );
        $newData = array();
        foreach ($variableGroup[$type] as $varibale) {
            $newData[$varibale] = isset($data[$varibale]) ? $data[$varibale] : null;
        }
        return $newData;
    }

    //clone products from category1 to category2
    public function cloneCategoryA2CategoryB($c1,$c2){
        $categoryA = Ex_category::find($c1);
        $products = $categoryA->products;
        foreach($products as $product){
            $product->categorys()->attach($c2);
        }
    }
    /*
     * Common functions end
     */

    /*Save product from category*/
    public function saveProduct2Category(Request $request){
        $this->validate($request,[
            'category_id'=>'required'
        ]);

//        dd($request->input('modelnum'));
        $category = Ex_category::find($request->input('category_id'));
        $product = Ex_product::where('model',trim($request->input('modelnum')))->first();
        $category->products()->attach($product->product_id);
        return redirect($_SERVER['HTTP_REFERER']);
    }

    /*
     * List product form specific category*/
    public function listProductFromCategory(){
        $result = null;
        $category_id = 0;
        $category_name = '';
        if(Input::has('id')){
            $categorySpecific = Ex_category::find(Input::get('id'));
            $products = $categorySpecific->products()->where('status',1)->get();
            foreach($products as $product){

                $product_detail = $product->description;
                $special = Ex_speceal::where('product_id', $product->product_id)->first();
                $status = '';

                if (isset($special->price)) {
                    if ($special->date_end <> '0000-00-00') {
                        $enddate = Carbon::parse($special->date_end);
                        $startdate = Carbon::parse($special->date_start);
                        $now = Carbon::now();
                        if ($now->between($startdate, $enddate)) {
                            $special = $special->price * 1.15;
                        } else {
                            $special = 0;
                        }

                    } else {
                        $special = $special->price * 1.15;
                    }
                }
                if($product->status==0){
                    $status = 'danger';
                }
                $result[] = compact('product','product_detail','special','status');
            }
            $category_id = Input::get('id');
            $category_name = self::categoryFullPath($categorySpecific);

        }
        $categorys = self::categorysFullPath();
//        $categorys = null;
        $categorys = \GuzzleHttp\json_encode($categorys);


//        echo $categorys;
        return view('listProductFromCategory',compact('categorys','result','category_id','category_name'));
    }
    /*
     * return array with all category full path*/
    public function categorysFullPath(){
        $categorys = array();
        $categorylist = Ex_category::all();
        foreach ($categorylist as $item){
            $tem = array();
            $tem['id'] = $item->category_id;

            $tem['name']=self::categoryFullPath($item);
            $tem['status'] = $item->status==0?'list-group-item-danger':'';
            $categorys[] = $tem;
        }
        return $categorys;
    }
    /*
     * return full category path*/
    private function categoryFullPath(Ex_category $category){
        $string = $category->description->name;
        $parent = $category->parentCategory();
        while (!empty($parent)) {
            $string = $parent->description->name.'->'.$string;

            $parent = $parent->parentCategory();
        }
        return htmlspecialchars_decode($string);
    }

    /*
     * delete product from category*/
    public function deleteProductFromCategory($category_id,$product_id){
        $category = Ex_category::find($category_id);
        $category->products()->detach($product_id);
        return redirect($_SERVER['HTTP_REFERER']);
    }
    /*
     * Batch edit product price by category*/
    public function batchEditPrice($category_id){
        $result = null;
        $quantity = 0;
        $average_cost = 0;


        $categorySpecific = Ex_category::find($category_id);
        $products = $categorySpecific->products()->where('status',1)->get();
        $url = env('SNPORT') . "?action=proprice";
        $content = self::getContent($url);
        $content = str_replace(',}', '}', $content);
        $content = \GuzzleHttp\json_decode($content, true);
        foreach($products as $product){
            if(Input::has('stock')){
                if($product->quantity<1){
                    continue;
                }
            }
            $code = $product->model;
            $product_detail = $product->description;
            $name = $product_detail->name;
            $product_id = $product->product_id;
            $special = Ex_speceal::where('product_id', $product->product_id)->first();
            $price = $product->price;
            if (isset($special->price)) {
                $quantity = 0;
                $average_cost = 0;
                if ($special->date_end <> '0000-00-00') {
                    $enddate = Carbon::parse($special->date_end);
                    $startdate = Carbon::parse($special->date_start);
                    $now = Carbon::now();
                    if ($now->between($startdate, $enddate)) {
                        $special = $special->price * 1.15;
                    } else {
                        $special = 0;
                    }

                } else {
                    $special = $special->price * 1.15;
                }
            }

            if(isset($content[$product->model])){
                $quantity = $content[$product->model][1];
                $average_cost = $content[$product->model][0];
            }
            $result[] = compact('special','quantity','average_cost','code','name','price','product_id');
        }

        $category_name = self::categoryFullPath($categorySpecific);



        $categorys = self::categorysFullPath();
        $categorys = \GuzzleHttp\json_encode($categorys);
        $result = \GuzzleHttp\json_encode($result);




        return view('batchEditProductPrice',compact('categorys','result','category_id','category_name','content'));
    }

    /*
     * accept batch product price */
    public function batchPriceEdit(Request $request){
//        dd($request->all());
        $this->validate($request,[
            'confirm-edit'=>'required'
        ]);
        if($request->has('product_id') && count($request->input('product_id'))>0){
            $product_id_array = $request->input('product_id');
            $product_base_price_array = $request->input('base_price');
            $product_special_price_array = $request->input('special_price');
            $num = count($request->input('product_id'));

            for($i = 0;$i<$num;$i++){

                $product = Ex_product::find($product_id_array[$i]);
                if(!empty($product_base_price_array[$i])){
                    $product->price = $product_base_price_array[$i] / 1.15;
                    $product->save();
                }

                if(!empty($product_special_price_array[$i])){
                        Ex_speceal::where('product_id', $product_id_array[$i])->delete();

                        $special = new Ex_speceal();
                        $special->product_id = $product_id_array[$i];
                        $special->customer_group_id = 1;
                        $special->priority = 0;
                        $special->price = $product_special_price_array[$i] / 1.15;
                        $special->date_start = "0000-00-00";

                        $special->date_end = "0000-00-00";

                        $special->save();


                }else if(($product_special_price_array[$i]=='')){
                    Ex_speceal::where('product_id', $product_id_array[$i])->delete();
                }
            }
        }
        return redirect(url('batchEditPrice',[$request->input('category_id')]));
    }
    /*
     * Flash sale*/
    public function show_flash_sale(){
        $products = Flash_sale_products::all();
        return view('flash_sale',compact('products'));
    }

    public function flash_sale_price_edit($code,$price){
        $product = Flash_sale_products::where('code',$code)->first();
        $product->price = $price/1.15;
        $product->save();

    }
    public function flash_sale_qty_edit($code,$qty){
        $product = Flash_sale_products::where('code',$code)->first();
        $product->qty = $qty;
        $product->save();
    }

    public function flash_sale_rrp_edit($code,$rrp){

        $product = Flash_sale_products::where('code',$code)->first();

        $product->rrp = $rrp;
        $product->save();


    }

    public function add_flash_sale_product(Request $request){

        if($request->has('code')){
            $code = trim($request->input('code'));
            $ex_product = Ex_product::where('model',$code)->first();


            if(is_null($ex_product)){
                return redirect("flash_sale");
            }
            $desciption = $ex_product->description;

            $product = new Flash_sale_products();
            $product->price = $ex_product->price;
            $product->starttime = Carbon::now();
            $product->code = $code;
            $product->content = $desciption->name;
            $product->product_id = $ex_product->product_id;
            $product->qty = $ex_product->quantity;
            $product->save();

            return redirect("flash_sale");


        }
    }

    public function flash_sale_product_del($id){
        Flash_sale_products::destroy($id);
    }

    public function publishFlash(){

        $category = Ex_category::find(307);
        $products = Flash_sale_products::all();
        $data = array();
        foreach ($products as $product){
            $data[] = $product->product_id;
            self::signProduct2Flash($product->product_id,$product->price,$product->qty);
        }
        $category->products()->sync($data);
        return redirect("flash_sale");

    }
    private function signProduct2Flash($product_id,$price,$qty){
        Ex_speceal::where('product_id',$product_id)->delete();
        $ex_product = Ex_product::find($product_id);
        $special = new Ex_speceal();
        $special->product_id = $ex_product->product_id;
        $special->customer_group_id = 1;
        $special->priority = 0;
        $special->price = $price;
        $special->date_end = Carbon::now()->addDay(1)->format('Y-m-d');
//        $special->date_end = "2017-01-02";
        $ex_product->jan = $ex_product->stock_status_id;
        $ex_product->stock_status_id = 31;
        $ex_product->quantity = $qty;
        $ex_product->save();
        $special->save();
    }

    private function unsignProductFromFlash($product_id){
        Ex_speceal::where('product_id',$product_id)->delete();
        $ex_product = Ex_product::find($product_id);
        $ex_product->stock_status_id = $ex_product->jan;
        $ex_product->jan = '';
        $ex_product->save();

    }


    public function offlineFlash(){
        $category = Ex_category::find(307);
        $products = $category->products;
        foreach ($products as $product){
            self::unsignProductFromFlash($product->product_id);
        }

        $category->products()->sync([]);
        return redirect("flash_sale");


    }
    /*
     * Flash sale end*/

    /*
     * quantity 0 products delete special price*/
    private function selfClearSpecial(){
        $specials = Ex_speceal::all();
        foreach ($specials as $item){
            $product = Ex_product::find($item->product_id);
            if($product->quantity<1){
                $item->delete();
            }
        }
    }

    /*Christmas sale setting: put special products in different category*/
    public function christmas_sale_setting(){
        self::selfClearSpecial();
        $category_id = 298;
        $category = Ex_category::find($category_id);
        $product_ids = array();
        $specials = Ex_speceal::all();
        foreach ($specials as $item){
            $product = Ex_product::find($item->product_id);
            if($product->quantity<1){
                continue;
            }
            $product_ids[] = $item->product_id;
        }
        $category->products()->sync($product_ids);
//        <<<<put all products into newsletter category
        $category_id = 309;
        $category = Ex_category::find($category_id);
        $product_ids = array();
        $specials = Ex_speceal::all();
        foreach ($specials as $item){
            $product = Ex_product::find($item->product_id);
            if($product->quantity<1){
                continue;
            }
            $percentage = ($product->price-$item->price)/$product->price;
            $percentage = round($percentage,2);

            if($percentage>=0.51){
                $product_ids[] = $item->product_id;
            }

        }
        $category->products()->sync($product_ids);
//        <<<<put all products into up70 category
        $category_id = 310;
        $category = Ex_category::find($category_id);
        $product_ids = array();
        $specials = Ex_speceal::all();
        foreach ($specials as $item){
            $product = Ex_product::find($item->product_id);
            if($product->quantity<1){
                continue;
            }
            $percentage = ($product->price-$item->price)/$product->price;
            $percentage = round($percentage,2);
            if($percentage>=0.41 && $percentage < 0.51){
                $product_ids[] = $item->product_id;
            }

        }
        $category->products()->sync($product_ids);
//        <<<<put all products into up50 category
        $category_id = 311;
        $category = Ex_category::find($category_id);
        $product_ids = array();
        $specials = Ex_speceal::all();
        foreach ($specials as $item){
            $product = Ex_product::find($item->product_id);
            if($product->quantity<1){
                continue;
            }
            $percentage = ($product->price-$item->price)/$product->price;
            $percentage = round($percentage,2);
            if($percentage>=0.31 && $percentage < 0.41){
                $product_ids[] = $item->product_id;
            }

        }
        $category->products()->sync($product_ids);
//        <<<<put all products into up40 category
        $category_id = 312;
        $category = Ex_category::find($category_id);
        $product_ids = array();
        $specials = Ex_speceal::all();
        foreach ($specials as $item){
            $product = Ex_product::find($item->product_id);
            if($product->quantity<1){
                continue;
            }
            $percentage = ($product->price-$item->price)/$product->price;
            $percentage = round($percentage,2);
            if($percentage>=0.21 && $percentage < 0.31){
                $product_ids[] = $item->product_id;
            }

        }
        $category->products()->sync($product_ids);
//        <<<<put all products into up30 category
        $category_id = 313;
        $category = Ex_category::find($category_id);
        $product_ids = array();
        $specials = Ex_speceal::all();
        foreach ($specials as $item){
            $product = Ex_product::find($item->product_id);
            if($product->quantity<1){
                continue;
            }
            $percentage = ($product->price-$item->price)/$product->price;
            $percentage = round($percentage,2);
            if($percentage < 0.21){
                $product_ids[] = $item->product_id;
            }

        }
        $category->products()->sync($product_ids);
//        <<<<put all products into up20 category
        $category_id = 314;
        $category = Ex_category::find($category_id);
        $product_ids = array();
        $specials = Ex_speceal::all();
        foreach ($specials as $item){
            $product = Ex_product::find($item->product_id);
            if($product->quantity<1){
                continue;
            }
            $price = $item->price*1.15;
            if($price<=1000 && $price>500){
                $product_ids[] = $item->product_id;
            }

        }
        $category->products()->sync($product_ids);
//        <<<product under 1000
        $category_id = 315;
        $category = Ex_category::find($category_id);
        $product_ids = array();
        $specials = Ex_speceal::all();
        foreach ($specials as $item){
            $product = Ex_product::find($item->product_id);
            if($product->quantity<1){
                continue;
            }
            $price = $item->price*1.15;
            if($price<=500 && $price>100){
                $product_ids[] = $item->product_id;
            }

        }
        $category->products()->sync($product_ids);
        $category_id = 316;
        $category = Ex_category::find($category_id);
        $product_ids = array();
        $specials = Ex_speceal::all();
        foreach ($specials as $item){
            $product = Ex_product::find($item->product_id);
            if($product->quantity<1){
                continue;
            }
            $price = $item->price*1.15;
            if($price<=100 && $price>50){
                $product_ids[] = $item->product_id;
            }

        }
        $category->products()->sync($product_ids);
        $category_id = 317;
        $category = Ex_category::find($category_id);
        $product_ids = array();
        $specials = Ex_speceal::all();
        foreach ($specials as $item){
            $product = Ex_product::find($item->product_id);
            if($product->quantity<1){
                continue;
            }
            $price = $item->price*1.15;
            if($price<=50){
                $product_ids[] = $item->product_id;
            }

        }
        $category->products()->sync($product_ids);

    }
    public function sunTotal(){
        $start = Carbon::parse('2014-01-05');
        $end = Carbon::parse('2017-01-01');
        $total = 0;
        while($start->lt($end)){
            $url = env('SNPORT') . "?action=invtotal&start=".$start->format('Y-m-d')."&end=".$start->addDay()->format('Y-m-d');

            $subtotal = floatval($this->getContent($url));

//
            $total += $subtotal;
            $start->format('Y-m-d');

//            echo '<br>';
            $start = $start->addDays(6);
        }
        echo round($total,2);
    }

    public function putProducts2Base(){
        self::clearMoreCaregory();
        $products = Ex_product::all();
        $productList = array();
        foreach ($products as $product){
            if (is_null($product->special)){
                $productList[] = $product->product_id;
            }
        }
        $category = Ex_category::find(327);
        $category->products()->sync($productList);
        echo 'success';
    }

    private function clearMoreCaregory(){
        $category = Ex_category::find(263);
        $products = $category->products;
        foreach ($products as $item){
            if ($item->quantity<1){
                $item->status=0;
                $item->save();
            }
        }
    }

    public function findMissProduct(){
//        echo 'test';
        $rocs = self::syncqty();
//        dd($rocs);
        $total = 0;
        foreach ($rocs as $code=>$pro){
            if ($pro[0]=='False' && $pro[1]>0){
                $ex_product = Ex_product::where('model',$code)->first();
                if (is_null($ex_product)){
                    $total++;
                    echo $code.'<br>';
                }

            }

        }
        echo 'Total: '.$total;
    }
}
