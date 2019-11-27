<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class News_letter extends Model
{
    	protected $table = 'news_letter';
	
	protected $fillable = array(
		'firstname','lastname','email'
	);
}
