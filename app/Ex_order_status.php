<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ex_order_status extends Model
{
    protected $connection = 'extremepc_mysql';
    protected $table = 'oc_order_status';
    protected $primaryKey = 'order_status_id';
    public $timestamps = false;
}
