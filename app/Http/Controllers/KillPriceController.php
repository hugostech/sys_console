<?php

namespace App\Http\Controllers;
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
    public function startKillPrice(){
        $product = null;
        if(Input::has('id')) {
            $product = Ex_product::find(Input::get('id'));
        }

        return view('killprice.startKill',compact('product'));

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

        }else{
            return redirect()->back()->withErrors(['pricespy', 'Price spy url not correct']);;
        }
//        dd($priceList);
        return view('killprice.confirm',compact('priceList','product','url'));
    }
    public function killpriceConfirm(Request $request){
//        dd($request->all());
        $this->validate($request,[
            'bottomPrice'=>'required'
        ]);
        $kill_price_product = Kill_price_product::create($request->all());
        if ($request->has('companies')){
            $kill_price_product->target = \GuzzleHttp\json_encode($request->input('companies'));
            $kill_price_product->save();
        }
        return redirect('killprice');

    }

    public function remove($id){
        Kill_price_product::destroy($id);
//        $product->delete();
        return redirect()->back();
    }
    public function listAllProducts(){
        $products = Kill_price_product::all();
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

    public function editPrice(){

    }

    public function getPriceList($page){
        $table = $page->find('div[id=tabcontentdiv]',0)->find('table',0)->find('tr[data-pris_typ=normal]');
        $result = [];


        foreach ($table as $item){

            $company = $item->find('td',0)->find('span',0)->plaintext;

            $price = $item->find('td',4);
            if(is_null($price)){
                continue;
            }
            $price = $price->children(1)->plaintext;
            $price = floatval(str_replace('$','',trim($price)));

            $result[] = [$company,$price];


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
    private function price_generate($price,$shift=1){
        if(floor($price) < $price){
            return floor($price);
        }else{
            return $price-$shift;
        }
    }
    private function edit_price(Ex_product $product,$price,$bottom,$warrany){
        $new_price = $this->price_generate($price);
        if ($new_price>$bottom){
            $product->price = ($new_price)/1.15;
        }else{
            $product->price = $bottom/1.15;
            $warrany[] = $product->model;
            $this->add_note($product,'<font color="yellow">Warning: Price below bottom price</font>');

        }
        $product->save();
        return $warrany;
    }
    private function add_note(Kill_price_product $product,$note){
        $product->note = $note;
        $product->save();
    }
    public function run(){

        Kill_price_product::where('status','y')->chunk(20,function($products){

            $warrany = [];
            foreach ($products as $product){
                try{


                DB::beginTransaction();
                $ex_product = Ex_product::find($product->product_id);

                if($ex_product->quantity<1) {
                    $this->add_note($product,'<font color="red">Stop: product no stock</font>');
                    continue;
                }
                $page = HtmlDomParser::file_get_html($product->url);
                $compantlist = $this->getPriceList($page);
//                dd($product->target);
                if (!is_null($product->target)){
                    $target = \GuzzleHttp\json_decode($product->target,true);
                    foreach ($compantlist as $company){
                        if (in_array($company[0],$target)){
                            $warrany = $this->edit_price($ex_product,$company[1],$product->bottomPrice,$warrany);
                            break;
                        }
                    }
                }else{
//                    dd($compantlist[0][0]);
                    if ($compantlist[0][0] != 'ExtremePC' && $compantlist[0][0] != 'Ktech'){
                        $warrany = $this->edit_price($ex_product,$compantlist[0][1],$product->bottomPrice,$warrany);

                    }
                }
                $this->add_note($product,'<font color="#228b22">Normal: update at'.Carbon::now().'</font>');
                DB::commit();
                }catch (\Exception $e){
                    $this->add_note($product,$e->getMessage());
                }

            }
        });
        return view('killprice.run');
    }
}
