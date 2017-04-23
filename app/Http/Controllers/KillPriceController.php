<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Psy\Exception\FatalErrorException;
use Sunra\PhpSimple\HtmlDomParser;
//use PHPHtmlParser\dom;
use App\Http\Requests;

class KillPriceController extends Controller
{
    public function step1(Request $request){

        $this->validate($request,[
            'pricespy_url'=>'required'
        ]);
        $url = $request->input('pricespy_url');
        $page = HtmlDomParser::file_get_html($url);
        $info = $page->find('div[id=product_content]',0);
        if (isset($info)){
            $priceList = self::getPriceList($page);

        }else{
            return redirect()->back()->withErrors(['pricespy', 'Price spy url not correct']);;
        }
//        dd($priceList);
        return view('killprice.confirm',compact('priceList'));
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
}
