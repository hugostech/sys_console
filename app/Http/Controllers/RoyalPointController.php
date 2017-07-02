<?php

namespace App\Http\Controllers;

use App\Ex_order;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Mail;

class RoyalPointController extends Controller
{
    public function sendNewRoyalPointReminder($order_id){
        $order = Ex_order::find($order_id);
        Mail::send('email.royalpoint.addpointnotice', compact('order'), function ($m) use ($order){
            $m->from('no-reply@extremepc.co.nz', 'ROC TECH LTD T/A ExtremePC');
//            $m->bcc('tony@roctech.co.nz', 'Tony Situ');
//            $m->bcc('hugo@roctech.co.nz', 'Hugo Wang');
            $email = $order->email;
            $name = $order->firstname.' '.$order->lastname;
            $m->to($email,$name)->subject('RE: Purchase order '.$order->order_id);
        });
    }
}
