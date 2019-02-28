<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Make_Models extends Model
{
    public function make() {
        return $this->belongsTo('App\\Makes','make_id');
    }
}
