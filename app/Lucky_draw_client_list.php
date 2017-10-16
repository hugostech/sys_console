<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lucky_draw_client_list extends Model
{
    protected $table='lucky_draw_list';
    protected $fillable = array(
        'name','email','phone'
    );
}
