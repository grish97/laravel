<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Makes extends Model
{
    protected $table = 'make';

    public function model() {
        return $this->hasManyThrough(Models::class,Make_Models::class,'model_id','id','make_id');
    }

    public function year() {
        return $this->hasOne(Vehicle::class,'make_id','id');
    }
}
