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
//        echo Input::get('echostr');
        if(Input::has('echostr')){
            $this->signature = Input::get('signature');
            $this->timestamp = Input::get('timestamp');
            $this->nonce = Input::get('nonce');
            if ($this->vaild()){
                return Input::get('echostr');
            }else{
                return false;
            }


        }
    }

    public function entry(Request $request){
        if(Input::has('echostr')){
            $this->signature = Input::get('signature');
            $this->timestamp = Input::get('timestamp');
            $this->nonce = Input::get('nonce');
            if ($this->vaild()){
                return Input::get('echostr');
            }else{
                return false;
            }


        }
    }

    private function vaild(){
        $arr = array(
            'sfcwechat',
            $this->timestamp,
            $this->nonce
        );
        sort($arr,SORT_STRING);
        $str = implode($arr);


        return sha1($str)==$this->signature;


    }
}
