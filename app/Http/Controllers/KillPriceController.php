<?php

namespace App\Http\Controllers;
use App\Ex_speceal;
use App\Kill_price_product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Psy\Exception\FatalErrorException;
use Sunra\PhpSimple\HtmlDomParser;
//use PHPHtmlParser\dom;
use App\Http\Requests;
use App\Ex_product;

class KillPriceController extends Controller
{
    private $killlist;

    public function __construct()
    {
        $this->killlist = array(
            'PB Technologies ','Playtech ','Computer Lounge ',
            'DTC Systems ','XP Computers Warehouse ','PC Force ',
            'Just Laptops ','Global PC ','Noel Leeming ','JB Hi-Fi ',
            'The Warehouse ','Paradigm PC\'s ','Ascent ','Mighty Ape ',
            'PC Online Shop '
        );
    }

    public function startKillPrice(){
        $product = null;
        $url = null;
        if(Input::has('id')) {
            $product = Ex_product::find(Input::get('id'));
            $kill_price = Kill_price_product::where('product_id',$product->product_id)->first();
            if(isset($kill_price)){
                $url = $kill_price->url;

            }

        }


        return view('killprice.startKill',compact('product','url'));

    }
    public function step1(Request $request){

        $this->validate($request,[
            'pricespy_url'=>'required'
        ]);
        $product = null;
        if($request->has('code')){
            $product = Ex_product::where('model',$request->input('code'))->first();
        }elseif ($request->has('product_id')){
            $product = Ex_product::find($request->input('product_id'));
        }else{
            return redirect()->back();
        }

        $url = $request->input('pricespy_url');
        $page = HtmlDomParser::file_get_html($url);
        $info = $page->find('div[id=product_content]',0);
        if (isset($info)){
            $priceList = self::getPriceList($page);
            $product_name = $info->find('h1[class=intro_header]',0)->plaintext;

        }else{
            return redirect()->back()->withErrors(['pricespy', 'Price spy url not correct']);;
        }
        $product_detail = $this->grabProductDetail($product->model);
        $averageCost = null;
        if(str_contains($product_detail,'Average price inc')){
            $productDetailArray = explode('<br>',$product_detail);
            $averageCost = str_replace('Average Cost: $','',$productDetailArray[4]);
            $averageCost = str_replace(',','',$averageCost);
            $averageCost = floatval($averageCost);
            $averageCost = $averageCost * 1.05 * 1.15;
//            $averageCost = number_format($averageCost, 2, '.', '');
            $averageCost = round($averageCost,2);
        }
        return view('killprice.confirm',compact('priceList','product','url','product_name','product_detail','averageCost'));
    }
    public function killpriceConfirm(Request $request){
//        dd($request->all());
        $this->validate($request,[
            'bottomPrice'=>'required'
        ]);
        $product_exist = Kill_price_product::where('status','y')->where('product_id',$request->input('product_id'))->first();
        if (count($product_exist) > 0){

            $product_exist->update($request->all());
            if ($request->has('companies')) {
                $product_exist->target = \GuzzleHttp\json_encode($request->input('companies'));
                $product_exist->save();
            }
        }else{
            $kill_price_product = Kill_price_product::create($request->all());
            if ($request->has('companies')) {
                $kill_price_product->target = \GuzzleHttp\json_encode($request->input('companies'));
                $kill_price_product->save();
            }
        }

        return redirect('killprice');

    }

    public function remove($id){

        $product = Kill_price_product::find($id);
        $product->status = 'n';
        $product->save();
//        $product->delete();
        return redirect()->back();
    }
    public function listAllProducts(){

        $products = Kill_price_product::where('status','y')->get();
        if (Input::has('filter')){
            if (Input::get('filter')=='error'){
                $tem = array();
                foreach ($products as $product){
                    $ex_product = Ex_product::find($product->product_id);
                    $special = $ex_product->special;
                    if (is_null($special)){
                        $price = round($ex_product->price*1.15,2);
                    }else{
                        $price = round($special->price*1.15,2);
                    }
                    if($price<$product->bottomPrice){
                        $tem[] = $product;
                    }
                }
                $products = $tem;
            }elseif (Input::get('filter')=='bottom'){
                $tem = array();
                foreach ($products as $product){
                    $ex_product = Ex_product::find($product->product_id);
                    $special = $ex_product->special;
                    if (is_null($special)){
                        $price = round($ex_product->price*1.15,2);
                    }else{
                        $price = round($special->price*1.15,2);
                    }
                    if($price==$product->bottomPrice){
                        $tem[] = $product;
                    }
                }
                $products = $tem;
            }


        }
        return view('killprice.list',compact('products'));
    }
    public function getPrice(){

        try{
            $url = "https://pricespy.co.nz/product.php?p=4020337";
//            $dom = new Dom;
            $page = HtmlDomParser::file_get_html($url);
//            $dom->loadFromUrl($url);
//            echo $dom->outerHtml;
            $priceList = self::getPriceList($page);

            $info = $page->find('div[id=product_content]',0);
            if (isset($info)){
                return $product_name = $info->find('h1[class=intro_header]',0)->plaintext;

            }else{
                return null;
            }
            echo $product_name;
        }catch (\Exception $e){
            echo null;

        }

    }

    public function grabProductDetail($code){
        $url = env('SNPORT') . "?action=test&code=$code";

//        dd($url);
        $pricedetail = $this->getContent($url);

//        $averageCost = 0;
//        if(str_contains($pricedetail,'Average price inc')){
//            $productDetailArray = explode('<br>',$pricedetail);
//            $averageCost = str_replace('Average Cost: $','',$productDetailArray[4]);
//            $averageCost = str_replace(',','',$averageCost);
//        }
        return $pricedetail;
    }

    public function getPriceList($page){

        $table = $page->find('div[id=tabcontentdiv]',0);
        if (is_null($table)) return null;
        $table = $table->find('table',0);
        if (is_null($table)) return null;

        $table = $table->find('tr[data-pris_typ=normal]');
        if (is_null($table)) return null;

        $result = [];


        foreach ($table as $item){
            try{

                $company = $item->find('td',0);
                if (is_null($company)) continue;
                $company = $company->find('span',0)->plaintext;

                $price = $item->find('td',4);
                if(is_null($price)){
                    continue;
                }
                $price = $price->children(1)->plaintext;
                $price = str_replace(',','',$price);
                $price = floatval(str_replace('$','',trim($price)));

                $result[] = [$company,$price];
            }catch (\Exception $e){
                continue;
            }



        }
        return $result;





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
    private function price_generate($price,$shift=0.5){
        if(floor($price) < $price){
            return floor($price);
        }else{
//            return $price-$shift;
            return $price;
        }
    }
    private function edit_price(Ex_product $product,$price,$bottom,$warrany){
        $new_price = $this->price_generate($price);
//        echo "<br>".$new_price."-".$bottom;
//        dd($product);
//        dd($new_price);
//        dd($bottom);
//        dd($this->price_generate($price));

        $special = $product->special;
//        echo is_null($special)."<br>";
        if(is_null($special)){
            $special = new Ex_speceal();
//            $special = new Ex_speceal();
            $special->product_id = $product->product_id;
            $special->customer_group_id = 1;
            $special->priority = 0;
//                    dd($this->price_generate($price));

            $special->price = $new_price / 1.15;
            $special->save();

        }

        if ($new_price>$bottom){
            $special->price = ($new_price)/1.15;
        }else{
            $special->price = $bottom/1.15;
            $warrany[] = $product->model;

//            $this->add_note($product,'<font color="yellow">Warning: Price below bottom price</font>');

        }
        $special->save();


        if (round($special->price,2) >= round($product->price,2)){
            $special->delete();
        }
//        dd($product);
        return $warrany;
    }
    private function add_note(Kill_price_product $product,$note){

        $product->note = $note;
        $product->save();
        echo $product->model.'<br>';

    }
    public function run(){

        Kill_price_product::where('status','y')->chunk(20,function($products){
            $warrany = [];

            foreach ($products as $product){
                try{

                DB::beginTransaction();

                $ex_product = Ex_product::find($product->product_id);

//                echo $ex_product->model .'-'.$ex_product->quantity;
                if (is_null($ex_product) || $ex_product->status == 0){

                    $product->status = 'n';
                    $product->save();
                    echo '____________1231231_____________'.$product->model;
                    DB::commit();

                    continue;
                }
                echo $ex_product->model.'<br>';
                if($ex_product->quantity<1) {

                    $special = $ex_product->special;

                    if (!is_null($special)){
                        $special->delete();
                    }
                    $this->add_note($product,'<font color="red">Stop: product no stock</font>');
                    DB::commit();
                    continue;
                }
                $page = HtmlDomParser::file_get_html($product->url);
                $compantlist = $this->getPriceList($page);
                if (is_null($compantlist)){
                    $this->add_note($product,'<font color="red">Stop: Page Error</font>');
                    DB::commit();
                    continue;
                }

                if (!is_null($product->target)){
//                if (true){
                    $target = \GuzzleHttp\json_decode($product->target,true);
//                    $target = $this->killlist;
                    foreach ($compantlist as $company){

                        if (in_array($company[0],$target)){
                            echo 'insert array<br>';
                            $warrany = $this->edit_price($ex_product,$company[1],$product->bottomPrice,$warrany);
                            break;
                        }
                    }
                }else{
//                    dd($compantlist[0][0]);
                    if ($compantlist[0][0] != 'ExtremePC' && $compantlist[0][0] != 'Ktech'){
                        $warrany = $this->edit_price($ex_product,$compantlist[0][1],$product->bottomPrice,$warrany);

                    }else{
                        foreach ($compantlist as $company){
                            if ($company[0] != 'ExtremePC' && $company[0] != 'Ktech') {
                                $warrany = $this->edit_price($ex_product, $company[1], $product->bottomPrice, $warrany);
                                break;
                            }
                        }
                    }
                }
                echo $ex_product->model.'step2<br>';
                $this->add_note($product,'<font color="#228b22">Normal: update at '.Carbon::now().'</font>');
                DB::commit();
                }catch(\Exception $e){
                    DB::rollback();
                    $this->add_note($product,$e->getMessage());

                }

            }
        });

        return view('killprice.run');
    }

    public function editBottomPrice(Request $request){
//        dd($request->all());
        $product = Kill_price_product::find($request->input('product_id'));
        $product->bottomPrice = $request->input('bottomPrice');
        $product->save();
        return $product->model.' price:'.$product->bottomPrice.' Success!';
    }


}
