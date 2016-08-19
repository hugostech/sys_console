<?php

namespace App\Http\Controllers;

use App\Category;
use App\category_item;
use App\Category_warranty;
use App\Eta;
use App\Ex_category;
use App\Ex_customer_address;
use App\Ex_manufacturer;
use App\Ex_order;
use App\Ex_order_history;
use App\Ex_product;
use App\Ex_product_category;
use App\Ex_product_description;
use App\Ex_product_related;
use App\Ex_product_store;
use App\Ex_speceal;
use App\Ex_stock_status;
use App\Http\Requests;
use App\News_letter;
use App\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mail;
use Mockery\CountValidator\Exception;

class unilityController extends Controller
{
    /*
     * funtions for kill price*/

    public function killshow()
    {
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
        foreach ($cats as $cat) {
            $content[$cat] = $this->getCat($cat)->total;
        }

        return view('killprice', compact('content'));
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
        $data = array();
        if ($request->has('code')) {
            self::addNewProduct($request->input('code'));
            $data = self::getData($request->input('code'));
        }

        return view('killprice', compact('data'));
    }

    private function getData($code)
    {
        $url = env('SNPORT') . "?action=test&code=$code";
        $pricedetail = $this->getContent($url);
        $url = env("SNPORT") . "?action=c&code=$code";
        $des = self::getContent($url);
        $product = Ex_product::where('model', $code)->first();
        $special = 0;
        $status = 0;
        if (isset($product->price)) {

            $extremepc = $product->price * 1.15;
            $extremepc = round($extremepc, 2);


            $special = Ex_speceal::where('product_id', $product->product_id)->first();
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

        $data = array(
            'code' => $code,
            'price' => $pricedetail,
            'special' => round($special, 2),
            'des' => $des,
            'extremepcprice' => $extremepc,
            'supplier_code' => $supplier_code,
            'status' => $status
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

            if (empty($request->input('special') * 1.0)) {
                Ex_speceal::where('product_id', $product->product_id)->delete();
            } else {
                Ex_speceal::where('product_id', $product->product_id)->delete();
                $special = new Ex_speceal();
                $special->product_id = $product->product_id;
                $special->customer_group_id = 1;
                $special->priority = 0;
                $special->price = $request->input('special') / 1.15;
                $special->date_start = "0000-00-00";
                $special->date_end = "0000-00-00";
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


            $data = self::getData($request->input('code'));
            return view('killprice', compact('data'));


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
     * batch change order status*/
    public function changeOrderStatus()
    {
        $orders = Ex_order::where('order_status_id', 15)->where('date_added', '<', '2016-07-01')->get();
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
//        self::producttosales();
        self::categoryarrange();
        return self::syncQuantity(); //sync quantity
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

//        $url = env('SNPORT')."?action=newclient";
//        $order = Ex_order::find($id);
//        $name = $order->firstname .' '.$order->lastname;
//        $email = $order->email;
//        $phone = $order->telephone;
//        $company = $order->shipping_company;
//        $address1 =  $order->shipping_address_1;
//        $address2 =  $order->shipping_address_2;
//        $city = $order->shipping_city;
//        $province = $order->shipping_zone;
//        $orderid = $id;
//        $ship_status = $order->shipping_method=='Free Shipping'?1:0;
//        $items = $order->items;
//        $roc_items = array();
//        foreach($items as $item){
//            $product = array(
//                'model'=>$item->model,
//                'name'=>$item->name,
//                'price'=>$item->price,
//                'quantity'=>$item->quantity,
//                'total'=>$item->total,
//                'tax'=>$item->tax
//            );
//            $roc_items[] = $product;
//        }
//        $roc_items = \GuzzleHttp\json_encode($roc_items);
//        $data = compact('name','email','phone','company','address1','address2','city','province','orderid','ship_status','roc_items');
//
//        echo self::sendData($url,$data);

    }

    public function addNewClient($id)
    {
        $url = env('SNPORT') . "?action=newclient";

        $order = Ex_order::find($id);
        $name = $order->firstname . ' ' . $order->lastname;
        $email = $order->email;
        $phone = $order->telephone;
        $company = addslashes($order->shipping_company);
        $address1 = addslashes($order->shipping_address_1);
        $address2 = addslashes($order->shipping_address_2);
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
        $company = addslashes($order->shipping_company);
        $address1 = addslashes($order->shipping_address_1);
        $address2 = addslashes($order->shipping_address_2);
        $city = addslashes($order->shipping_city) . ' ' . addslashes($order->shipping_zone);
        $orderid = '#' . $id;
        $comment = addslashes($order->comment);
        $ship_status = $order->shipping_method == 'Free Shipping' ? 1 : 0;
        $ship_fee = $order->shipfee();
        $data = compact('phone', 'company', 'address1', 'address2', 'city', 'orderid', 'ship_status', 'clientId', 'comment','ship_fee');
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

            $data = \GuzzleHttp\json_decode(self::getContent($url));
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
                $description->name = str_replace('{!@!}', '"', $data->name);
                $description->description = str_replace('{!@!}', '"', $data->spec);
                $description->meta_title = $data->name;
                $description->save();
                $category = new Ex_product_category();
                $category->product_id = $product->product_id;
                $category->category_id = 267;
                $category->save();
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
        $sales = Ex_product_category::where('category_id',272)->get();
        $result = array();
        foreach($sales as $sale){
            $product = Ex_product::find($sale->product_id);
            $product_detail = Ex_product_description::where('product_id',$sale->product_id)->first();
            $result[] = compact('product','product_detail');
        }
//        dd($result);
        return view('sales_list',compact('result'));

    }
    public function sales_add(Request $request){
        $models = $request->input('modelnum');
        foreach($models as $model){
            if(empty($model))
                continue;
            $product = Ex_product::where('model',$model)->first();
            if(isset($product)){



                $category_product  = new Ex_product_category();
                $category_product->category_id = 272;
                $category_product->product_id = $product->product_id;
                $category_product->save();



            }else{
                throwException('Can find model');
            }
        }
        return redirect('sales_list');

    }
    public function sales_remove($id){

        $category = Ex_product_category::where('category_id',272)->where('product_id',$id)->delete();
        return redirect('sales_list');

    }

    public function eta_list(){
        $etas = Eta::all();
        return view('eta_list',compact('etas'));
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
        foreach ($customers as $customer) {

            echo $customer->firstname . ' ' . $customer->lastname . ' ||| ' . $customer->address_1 . ' ' . $customer->address_2;

            if (count(Ex_order::where('customer_id', $customer->customer_id)->get()) > 0) {
                echo ' <font color="red">Yes</font> '.Ex_order::where('customer_id', $customer->customer_id)->sum('total').'<br>';
            }
        }
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
    /*
     * Common functions end
     */
}
