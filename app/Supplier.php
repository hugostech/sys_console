<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'suppliers';

    public function warrantys(){
        return $this->hasMany('App\Warranty','supplier_id','id');
    }
}
