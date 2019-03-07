<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $table = 'vehicle';

    public function model() {
        return $this->belongsTo(Models::class,'model_id','id');
    }
}