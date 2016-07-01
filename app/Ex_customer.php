<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ex_customer extends Model
{
    protected $connection = 'extremepc_mysql';
    protected $table = 'oc_ex_customer';
    protected $primaryKey = 'customer_id';

    public $timestamps = false;

    public function addresses(){
        return $this->hasMany('App\Ex_customer_address','customer_id');
    }
}
