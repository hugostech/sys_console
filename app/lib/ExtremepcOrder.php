<?php
/**
 * Created by PhpStorm.
 * User: Hugo
 * Date: 9/07/17
 * Time: 4:35 PM
 */

namespace backend;


use App\Ex_order;
use App\Ex_transaction;
use Carbon\Carbon;

class ExtremepcOrder
{
    private $order;

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param mixed $order
     */
    public function setOrder(Ex_order $order)
    {
        $this->order = $order;
    }

    public static function loadById($order_id){

        $order = Ex_order::find($order_id);

        if (is_null($order)){
            return null;
        }else{
            $instance = new self();
            $instance->setOrder($order);
            return $instance;
        }


    }

    public function giveRoyalPoint(){

        if ($this->order->order_status_id == 5 && $this->order->royal_point==0){
            if (in_array($this->order->payment_code, ['bank_transfer','poli'])){
                $amount = $this->order->payment_code=='bank_transfer'?round($this->order->total/50,4):round($this->order->total/100,4);
                $tansaction = array(
                    'customer_id'=>$this->order->customer_id,
                    'order_id'=>0,
                    'description'=>'Loyalty Points from Order:'.$this->order->order_id,
                    'amount'=>$amount,
                    'date_added'=>Carbon::now()
                );
                Ex_transaction::create($tansaction);
            }
            $this->order->royal_point = 1;
            $this->order->save();
        }
    }



}