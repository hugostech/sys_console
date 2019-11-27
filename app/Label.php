<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    protected $table = 'labels';
    protected $fillable = array(
        'price','description','type'
    );

    public function setDescriptionAttribute($value){
        if (is_array($value)){
            $this->attributes['description'] = \GuzzleHttp\json_encode($value);
        }else{
            $this->attributes['description'] = $value;
        }
    }
}
