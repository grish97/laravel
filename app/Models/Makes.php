<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Makes extends Model
{
    protected $table = 'make';

    public function model() {
        return $this->hasMany(Models::class,'id','id');
    }
}
