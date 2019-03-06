<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Makes extends Model
{
    protected $table = 'make';

    public function model() {
        return $this->hasMany(Models::class,'id','id');
    }

    public function vehicle() {
        return $this->hasOne(Vehicle::class,'make_id','id');
    }
}
