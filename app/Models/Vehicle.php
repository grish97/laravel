<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $table = 'vehicle';

    public function make() {
        return $this->hasOne(Makes::class,'id','make_id');
    }

    public function model() {
        return $this->hasMany(Models::class,'id','model_id');
    }

    public function type() {
        return $this->hasOne(Type::class,'id','type_id');
    }
}
