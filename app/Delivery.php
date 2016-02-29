<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    protected $table = 'deliverys';

    protected $fillable = array(
        'model_id','reference_no','track_number'
    );

    public function warranty(){
        return $this->belongsTo('App\Warranty','model_id');
    }
}
