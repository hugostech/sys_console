<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category_warranty extends Model
{
    protected $table = 'category_warranty';

    protected $fillable = array('supplier','detail','tag');

    public function categorys(){
        return $this->belongsToMany('App\Category','supplier_warranty','warranty_detail_id','category_id');
    }
}
