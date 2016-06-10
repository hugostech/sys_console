<?php

namespace App\Http\Controllers;

use App\Ex_product_category;
use App\Ex_product_description;
use Mail;
use App\Old_client;
use App\Category;
use App\News_letter;
use App\category_item;
use App\Category_warranty;
use App\Ex_product;
use App\Ex_product_store;
use App\Product;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Mockery\CountValidator\Exception;
use Sunra\PhpSimple\HtmlDomParser;
use App\Ex_speceal;

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

    public function killPrice(Request $request)
    {
        $data = array();
        if ($request->has('code')) {
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
            $special = isset($special->price) ? $special->price * 1.15 : 0;

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


    public function killPrice_edit(Request $request)
    {
        if ($request->has('code')) {
            $product = Ex_product::where('model', $request->input('code'))->first();
            $product->price = $request->price / 1.15;
            $product->save();

            if (empty($request->input('special') * 1.0)) {
                Ex_speceal::where('product_id', $product->product_id)->delete();
            } else {
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

    /*kill price functions end*/

    /*=====================================================================================================================*/

    /*price sync with pricespy
    unfinish*/

    /*
     * get category json data
     * param: cat = category, string*/

    public function addPricespyMap(Request $request)
    {
        if ($request->has('pricespy_url')) {
            $product = new Product();
            $product->code = $request->input('code');
            $product->description = $request->input('description');
            $product->pricespy_url = $request->input('pricespy_url');

        }
    }

    /*price sync pricespy end*/


    /*=====================================================================================================================*/


    /*
     * warranty guide functions
     * */
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

    public function warrantydetail($id)
    {
        $supplier = Category_warranty::find($id);
        return view('warrantydetail', compact('supplier'));
    }

    /*
     * warranty guide functions end*/

    /*=====================================================================================================================*/

    /*sync data from roctech to extremepc functions*/
    /*
     * sync the quantity from roctech to extremepc
     * */

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

    /*daily sync quantity*/
    public function syncQuantity()
    {
        $products = Ex_product::where('status', 1)->get();
        foreach ($products as $product) {
            $code = intval($product->model);
            if ($code != 0) {
                $url = env('SNPORT') . "?action=sync&code=$code";
                $quantity = $this->getContent($url);
                $pos = strpos($quantity, 'Error');
                if ($pos === false) {
                    $product->quantity = $quantity;

                } else {

                    //	$product->status = 0;
                }

                $product->save();
            }
        }
        $content = 'Last sync is at' . date(' jS \of F Y h:i:s A');
        return view('self_sync', compact('content'));
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

    public function addNewProduct($code)
    {
        if(self::checkCodeEx($code)){
            echo $code.' <font color="red">code exist</font>';
        }else{
            $url = env('SNPORT') . "?action=prosync&code=$code";

            $data = \GuzzleHttp\json_decode(self::getContent($url));
//        dd($data);
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
            $category->category_id = 263;
            $category->save();
            return $product->model.' <font color="green">Insert Sucessed</font>';
        }



    }

    private function imageCopy($code)
    {
        $url = env('IMGREMOTE') . $code . '.jpg';
        if(self::imageExist($url)){
            copy($url, "/var/www/extremepc.co.nz/public_html/image/catalog/autoEx/$code.jpg");
        }
    }

    public function grabProducts()
    {
        $url = env('SNPORT') . "?action=products";
        $content = self::getContent($url);
        $content = str_replace(',]',']',$content);
        $codes = \GuzzleHttp\json_decode($content);
        foreach($codes as $code){
            echo self::addNewProduct($code);
            echo '<br>';

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

    /*
     * news letter functions end*/


    /*=====================================================================================================================*/

    /*
     * Common functions*/
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

    private function checkCodeEx($code){
        if(count(Ex_product::where('model',$code)->get())>0){
            return true;
        }else{
            return false;
        }
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

    private function imageExist($url){

        $file_headers = @get_headers($url);
        if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
            $exists = false;
        }
        else {
            $exists = true;
        }
        return $exists;
    }
    /*
     * Common functions end
     */
}
