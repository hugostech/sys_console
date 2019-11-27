<?php

namespace App\Http\Controllers;

use App\Ex_order;
use backend\ExtremepcOrder;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Mail;

class RoyalPointController extends Controller
{
    public function sendNewRoyalPointReminder($order_id, $amount){
        $order = Ex_order::find($order_id);
        Mail::send('email.royalpoint.addpointnotice', compact('order','amount'), function ($m) use ($order){
            $m->from('sales@extremepc.co.nz', 'ROC TECH LTD T/A ExtremePC');
//            $m->bcc('tony@roctech.co.nz', 'Tony Situ');
//            $m->bcc('hugo@roctech.co.nz', 'Hugo Wang');
            $email = $order->email;
            $name = $order->firstname.' '.$order->lastname;
            $m->to($email,$name)->subject('RE: Purchase order '.$order->order_id);
        });
    }

    public function send_royal_point($order_id){
        $ex_order = ExtremepcOrder::loadById($order_id);
        if (!is_null($ex_order)){
            $amount = $ex_order->giveRoyalPoint();
            if($amount!=0){
                $this->sendNewRoyalPointReminder($order_id, $amount);
            }

        }
    }

    public function run($date_start = '2017-04-01'){
        Ex_order::where('date_added','>',$date_start)->where('royal_point',0)->where('order_status_id',5)->chunk(20,function ($orders){
            foreach ($orders as $order){
//                echo $order->order_id.'<br>';
                $this->send_royal_point($order->order_id);
            }
        });
    }
}
