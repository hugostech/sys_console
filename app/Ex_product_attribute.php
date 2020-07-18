<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ex_product_attribute extends Model
{
    protected $connection = 'extremepc_mysql';
    protected $table = 'oc_product_attribute';

    public $timestamps = false;

    public function setTextAttribute($value){
        switch ($this->attributes['attribute_id']){

            case 5:
            case 4:
            case 9:
                $value = "$value GB";
                break;
            case 6:
                $value = "$value inches";
                break;
            default:
                break;
        }
        $this->attributes['text'] = $value;

    }

    public function getTextAttribute($value){
        $value = str_replace('inches', '', $value);
        $value = str_replace('GB', '', $value);
        return trim($value);
    }
}
