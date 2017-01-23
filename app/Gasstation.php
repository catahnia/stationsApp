<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gasstation extends Model
{
    public function pricedata () {
    	return $this->hasMany('App\Pricedata');
    }

    public function user () {
    	return $this->belongsTo('App\User');
    }
    
}
