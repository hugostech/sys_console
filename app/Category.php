<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table='category';

    protected $fillable = array('name');

    public function suppliers(){
        return $this->belongsToMany('App\Category_warranty','supplier_warranty','category_id','warranty_detail_id');
    }


}
