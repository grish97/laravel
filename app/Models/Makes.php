<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Makes extends Model
{
    protected $table = 'make';

    public function model() {
        return $this->hasManyThrough(Models::class,Vehicle::class,'make_id','id','id');
    }

    public function year() {
        return $this->hasOne(Vehicle::class,'make_id','id');
    }
}
