<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Makes extends Model
{
    protected $table = 'make';

    public function model() {
        return $this->hasManyThrough(Models::class,Make_Models::class,'model_id','id','id','model_id');
    }
}
