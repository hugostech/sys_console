<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ex_customer_address extends Model
{
    protected $connection = 'extremepc_mysql';
    protected $table = 'oc_ex_address';
    protected $primaryKey = 'address_id';

    public $timestamps = false;
    public function customer(){
        return $this->belongsTo('App\Ex_customer','customer_id');
    }
}
