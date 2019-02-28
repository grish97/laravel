<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Makes extends Model
{
    protected $table = 'make';

    public function models() {
        return $this->hasOne('App\\Models\\Models');
    }

    public function make_models() {
        return $this->hasOne('App\\Make_Models');
    }
}
