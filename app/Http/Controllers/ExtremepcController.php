<?php

namespace App\Http\Controllers;

use App\Ex_order;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use App\Ex_order_history;

class ExtremepcController extends Controller
{
    public function ex_order_detail(){
        $order = null;
        if (Input::has('code')){
            $order = Ex_order::find(trim(Input::get('code')));
        }
        return view('ex_order_detail',compact('order'));
    }

    public function complete_order($order_id){

        $order = Ex_order::find($order_id);
        $history = new Ex_order_history();
        $history->order_id = $order->order_id;
        $history->order_status_id = 5;
        $history->notify = 0;
        $history->comment = 'Updated by system';
        $history->date_added = Carbon::now();
        $history->save();
        $order->order_status_id = 5;
        $order->date_modified = Carbon::now();
        $order->save();
        return redirect('ex_order_confirm');
    }
}
