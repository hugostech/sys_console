<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Input;

class WechatController extends Controller
{
    private $signature;
    private $timestamp;
    private $nonce;
    public function index(){
        if(Input::has('echostr')){
            $this->signature = Input::get('signature');
            $this->timestamp = Input::get('timestamp');
            $this->nonce = Input::get('nonce');
            if ($this->vaild()){
                return Input::get('echostr');
            }


        }else{
            return null;
        }
    }

    public function entry(Request $request){

    }

    private function vaild(){
        $arr = array(
            env('WCTOKEN'),
            $this->timestamp,
            $this->nonce
        );
        sort($arr,SORT_STRING);
        $str = implode($arr);
        return sha1($str)==$this->signature;


    }
}
