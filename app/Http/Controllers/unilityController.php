<?php

namespace App\Http\Controllers;

use Mail;
use App\Old_client;
use App\Category;
use App\News_letter;
use App\category_item;
use App\Category_warranty;
use App\Ex_product;
use App\Product;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Sunra\PhpSimple\HtmlDomParser;

class unilityController extends Controller
{
    public function killPrice(Request $request){
        $data = array();
        if($request->has('code')){
            $data = self::getData($request->input('code'));
        }

        return view('killprice',compact('data'));
    }

    public function killPrice_edit(Request $request){
	if($request->has('code')){
		$product = Ex_product::where('model',$request->input('code'))->first();
		$product->price = $request->price/1.15;
		$product->save();
            	$data = self::getData($request->input('code'));
        	return view('killprice',compact('data'));
	}
    }
    public function addPricespyMap(Request $request){
        if($request->has('pricespy_url')){
            $product = new Product();
            $product->code = $request->input('code');
            $product->description = $request->input('description');
            $product->pricespy_url = $request->input('pricespy_url');

        }
    }

    public function killshow(){
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
        foreach($cats as $cat){
//            var_dump($this->getCat($cat)->total);
            $content[$cat] = $this->getCat($cat)->total;
//            Session::put($cat, $content[$cat]);
        }

        return view('killprice',compact('content'));
    }
    /*
     * get category json data
     * param: cat = category, string*/
    private function getCat($cat){
        $url = env('SNPORT')."?action=ds&cat=$cat";
        $data = $this->getContent($url);
        return json_decode($data);
    }
    private function getData($code){
        $url = env('SNPORT')."?action=test&code=$code";
        $pricedetail = $this->getContent($url);
        $url = env("SNPORT")."?action=c&code=$code";
        $des = self::getContent($url);
	$product = Ex_product::where('model',$code)->first();
	if(isset($product->price)){

		$extremepc=$product->price*1.15;
		$extremepc = round($extremepc,2);
	}else{
		
            $extremepc = "Cannot find the product";
	}

        $url = env("SNPORT")."?action=sc&code=$code";
        $supplier_code = self::getContent($url);
/*
        $url = "http://www.extremepc.co.nz/index.php?main_page=advanced_search_result&keyword=$code";
        $extremepchtml = HtmlDomParser::file_get_html($url);
        $url = env("SNPORT")."?action=sc&code=$code";
        $supplier_code = self::getContent($url);

        try{
            $table = $extremepchtml->find('table[id=catTable]',0);
            if(!empty($table)){
                $extemepc = $table->find('font',0)->plaintext;
            }else{
                $extemepc = "Cannot find the product";
            }


        }catch(Exception $e){
            $extemepc = "Cannot find the product";

        }
*/
        $data = array(
            'code'=>$code,
            'price'=>$pricedetail,
            'des'=>$des,
            'extremepcprice'=>$extremepc,
            'supplier_code'=>$supplier_code
        );
        return $data;
    }

    public function showWarrantyGuide(){
        $categorys = Category::all();
        return view('warrantyguide',compact('categorys'));
    }

    public function warrantySubCategory($id){
        $category = Category::find($id)->name;
        $category_items = category_item::where('category_id',$id)->get();
        $suppliers = array();

        foreach($category_items as $item){
            $suppliers[] = Category_warranty::find($item->warranty_detail_id);
        }

        return view('warranty_subcategory',compact('category','suppliers'));
    }

    public function warrantydetail($id){
        $supplier = Category_warranty::find($id);
        return view('warrantydetail',compact('supplier'));
    }
    public function sync(Request $request){
        $match = 0;
        $quantity = 0;
        $disable = 0;
        $save = 0;
        if($request->has('quantity')){
            $quantity = 1;
        }
        if($request->has('disable')){
            $disable = 1;
        }
        if($request->has('match')){
            $match = 1;
        }
        if($quantity == 1 || $disable == 1){
            $save = 1;
        }
//        if($request->has('self')){
//            $self = 1;
//        }
        $status = $request->input('status');
        if($status == '2'){
            $products = Ex_product::where('status',1)->get();
        }else{
            $products = Ex_product::all();
        }

        $total = count($products);
        $int = 0;
        $unfound = array();
        foreach($products as $product){
            $code = $product->model;
            $url = env('SNPORT')."?action=sync&code=$code";
            $quantity = $this->getContent($url);
            $pos = strpos($quantity, 'Error');
            if($pos === false){
                if($quantity == 1){
                    $product->quantity = $quantity;
                }




            }else{
                if($disable == 1){
                    $product->status = 0;
                }
                $int++;
                $unfound[] = $code;
            }
            if($save == 1){
                $product->save();
            }
        }
        $result = array(
            'total' => $total,
            'int' => $int,
            'unfound' => $unfound
        );
        return view('sync',compact('result'));
    }
    /*
     * sync the quantity from roctech to extremepc
     * */
    public function syncQuantity(){
        $products = Ex_product::where('status',1)->get();
        foreach($products as $product){
       	    $code = intval($product->model);
	    if($code != 0){
            	$url = env('SNPORT')."?action=sync&code=$code";
            	$quantity = $this->getContent($url);
            	$pos = strpos($quantity, 'Error');
            	if($pos === false){
                	$product->quantity = $quantity;

            	}else{
			
                    //	$product->status = 0;
		}

                $product->save();
	    }
        }
	$content = 'Last sync is at'.date(' jS \of F Y h:i:s A');	
	return view('self_sync',compact('content'));
    }

    public function showSync(){
        return view('sync');
    }

    public function self_check(){
        $products = Ex_product::where('status',1)->get();
        $total = count($products);
        $int = 0;
        $content = '';
        foreach($products as $product){

            $code = $product->model;
            $url = env('SNPORT')."?action=sync&code=$code";
            $quantity = $this->getContent($url);
            $pos = strpos($quantity, 'Error');
            if(!$pos === false){
                $int++;

                $content .= $code;
                $content .= '<br>';
            }

        }
        $percentage = $int*1.0/$total;
        $percentage = round($percentage,2)*100;
        return view('sync',compact('content','percentage'));
    }

/*
news_letter one off job to transfer data from old roctech data
*/
	public function old_transfer(){

		$clients = Old_client::All();

		foreach($clients as $client){
			if(!empty($client->customers_email_address)){
				$news_letter = new News_letter();
				
				$news_letter->firstname = $client->customers_firstname;			
				$news_letter->lastname = $client->customers_lastname;			
				$news_letter->email = $client->customers_email_address;			
				$news_letter->save();
			}
		}
	}
    
    public function sendNewsLetter(){
	$newsletters = News_letter::All();

	//$newsletters = News_letter::where('email','hugowangchn@gmail.com')->get();
	foreach($newsletters as $user){
	 	Mail::send('newsletter',compact('user'), function ($m) use ($user){
            		$m->from('no-reply@extremepc.co.nz', 'Extreme PC');
	    		$m->replyTo('sales@roctech.co.nz', 'Sales Department');
            		$m->to($user->email)->subject('New Website Launch Deal Extreme PC');
        	});	

	}
    }
    public function unsubscribe($email){
	$unsubscribe = News_letter::where('email',$email)->first();
	if(!empty($unsubscribe->email)){

		$unsubscribe->status='false';
		$unsubscribe->save();
	}	
	echo 'Unsubscribe successful! Thanks';
    }

    private function getContent($url){

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
}
