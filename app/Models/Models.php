<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Models extends Model
{
   public function make() {
       return $this->belongsTo('App\\Makes');
   }
}
