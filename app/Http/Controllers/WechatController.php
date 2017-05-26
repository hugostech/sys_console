<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Input;

class WechatController extends Controller
{

//    public function index(){
////        echo Input::get('echostr');
//        if(Input::has('echostr')){
//            $token = 'sfcwechat';
//            $signature = Input::get('signature');
//            $timestamp = Input::get('timestamp');
//            $nonce = Input::get('nonce');
//            $array = array($token, $timestamp, $nonce);
//            sort($array, SORT_STRING);
//            $str = implode($array);
//            if(sha1($str) == $signature){
//                echo Input::get('echostr');
//            }else{
//                echo false;
//            }
//
//        }
//    }

    public function entry(Request $request){
        dd($request->all());
    }

//    private function vaild(){
//        $array = array(TOKEN, $timestamp, $nonce);
//        sort($array, SORT_STRING);
//        $str = implode($array);
//
//
//    }
}
