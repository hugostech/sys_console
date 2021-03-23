<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Warranty extends Model
{
    protected $table = 'warrantys';

    protected $fillable = array(
        'model_name','model_code','staff','client_name','client_phone','supplier_id','sn','storage','quantity','branch'
    );

    public function delivery(){
        return $this->hasOne('App\Delivery','model_id');
    }

    public function status(){
        return $this->hasMany('App\Status','model_id');
    }

    public function note(){
        return $this->hasMany('App\Note','model_id');
    }

    public function supplier(){
        return $this->belongsTo('App\Supplier','supplier_id');
    }
}
