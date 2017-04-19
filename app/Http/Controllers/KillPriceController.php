<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Psy\Exception\FatalErrorException;
use Sunra\PhpSimple\HtmlDomParser;
use App\Http\Requests;

class KillPriceController extends Controller
{
    public function getPrice(){

        try{
            $url = "https://pricespy.co.nz/#rparams=ss=44";
            $page = HtmlDomParser::file_get_html($url);
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
