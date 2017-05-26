<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
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
    private $token;
    public function __construct()
    {
        $this->token = $this->getAccessToken();
    }

    public function createKF(){

        $url = 'https://api.weixin.qq.com/customservice/kfaccount/add?access_token='.$this->token;
        $data = <<<KF
        {
            "kf_account" : "sfc@sfc_express",
            "nickname" : "Hugo"
         }
KF;
        echo $this->sendData($url,$data);
        return $this->bindKF();

    }

    public function bindKF(){
        $url = 'https://api.weixin.qq.com/customservice/kfaccount/inviteworker?access_token='.$this->token;

        $data = <<<DATA
         {
             "kf_account" : "sfc@sfc_express",
            "invite_wx" : "hugonj"
         }
DATA;
        return $this->sendData($url,$data);


    }

    public function createMenu(){

        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$this->token;
        $menu = <<<MENUSTYLE
        {
            "button": [
                {
                    "type": "view", 
                    "name": "关于快羊国际", 
                    "url": "http://shopfromchina.co.nz"
                }, 
                {
                    "name": "功能", 
                    "sub_button": [
                        {
                            "type": "view", 
                            "name": "转运", 
                            "url": "http://shopfromchina.co.nz/%E8%BD%AC%E8%BF%90/"
                        }, 
                        {
                            "type": "view", 
                            "name": "价格", 
                            "url": "http://shopfromchina.co.nz/%E4%BB%B7%E6%A0%BC/", 
                            
                        }, 
                        {
                            "type": "view", 
                            "name": "注册", 
                            "url": "http://dashboard.shopfromchina.co.nz/register"
                        }
                    ]
                }
            ]
        }
MENUSTYLE;

    }

    public function entry(Request $request){
        $ip = $request->input('ip');
        $ips = $this->getWcIPs();
//        if (in_array($ip,$ips)){
        if(true){
            $content = $request->input('content');

            return $this->run($content);
        }else{
            echo '非法访问';
        }
    }

    private function run($content){

        $content = html_entity_decode($content);
        $xml = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);
        if($xml->MsgType=='event'){
            return $this->eventHandle($xml->Event,$xml);
        }else{
            return $this->msgHandle($xml);
        }
    }

    private function msgHandle($xml){
        $to = $xml->ToUserName;
        $from = $xml->FromUserName;
        return $this->callKF($xml);
        $msg = '感谢您关注SFC快羊国际
        
            SFC快羊国际是新西兰中国商品购买及转运服务提供商，专门为新西兰用户提供代购中国商品一站式服务。采购、检验、保存、寄送、售后，所有环节我们解决并负责。
            
            我们坚持为用户解决难题、创造价值之理念，以最具美誉之产品与服务“重新定义新西兰购物”！';
        return $this->msgGenerator($from,$to,$msg);
    }
    private function callKF($xml){
        $content = <<<KF
         <xml>
             <ToUserName><![CDATA[$xml->ToUserName]]></ToUserName>
             <FromUserName><![CDATA[$xml->FromUserName]]></FromUserName>
             <CreateTime>%s</CreateTime>
             <MsgType><![CDATA[transfer_customer_service]]></MsgType>
         </xml>
KF;
        return sprintf($content,Carbon::now());

    }
    private function eventHandle($type,$xml){
        if ($type=='subscribe'){
            $to = $xml->ToUserName;
            $from = $xml->FromUserName;
            $msg = '感谢您关注SFC快羊国际
            
            SFC快羊国际是新西兰中国商品购买及转运服务提供商，专门为新西兰用户提供代购中国商品一站式服务。采购、检验、保存、寄送、售后，所有环节我们解决并负责。
            
            我们坚持为用户解决难题、创造价值之理念，以最具美誉之产品与服务“重新定义新西兰购物”！';
            return $this->msgGenerator($from,$to,$msg);



        }
    }

    private function msgGenerator($to,$from,$msg){
        $msgTemplate = <<<XMLMESSAGE
                <xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[text]]></MsgType>
                <Content><![CDATA[%s]]></Content>
                </xml>
XMLMESSAGE;
        return sprintf($msgTemplate,$to,$from,Carbon::now(),$msg);
    }

//    private function vaild(){
//        $array = array(TOKEN, $timestamp, $nonce);
//        sort($array, SORT_STRING);
//        $str = implode($array);
//
//
//    }
    private function getWcIPs(){
        $token = $this->getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=$token";
        $ips = $this->getContent($url);
        $ips = \GuzzleHttp\json_decode($ips,true);
        return $ips['ip_list'];
    }
    private function getAccessToken(){
        $appid = env('WC_APPID');
        $secret = env('WC_APPSECRET');
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";
        $output = $this->getContent($url);
        $arr = \GuzzleHttp\json_decode($output,true);
//        dd($arr['access_token']);
        return $arr['access_token'];

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

    private function sendData($url, $data)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);



        return $server_output;

    }
}
