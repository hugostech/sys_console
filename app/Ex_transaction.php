<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ex_transaction extends Model
{
    protected $connection = 'extremepc_mysql';
    protected $table = 'oc_customer_transaction';
    protected $primaryKey = 'customer_transaction_id';
    public $timestamps = false;
    protected $fillable=array(
        'customer_id','order_id','description','amount','date_added'
    );
}
