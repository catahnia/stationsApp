<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pricedata extends Model
{
    public function gasstation () {
    	return $this->belongsTo('App\Gasstation');
    }
}
