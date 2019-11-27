<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ex_customer extends Model
{
    protected $connection = 'new_extremepc_mysql';
    protected $table = 'oc_customer';
    protected $primaryKey = 'customer_id';

    public $timestamps = false;

    public function addresses(){
        return $this->hasMany('App\Ex_Newcustomer_address','customer_id');
    }

    public function orders(){
        return $this->hasMany('App\Ex_Neworder','customer_id');
    }
}
