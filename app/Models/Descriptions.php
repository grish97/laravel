<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Descriptions extends Model
{
    protected $table = 'description';

    public function part() {
        return $this->belongsTo(Parts::class,'id');
    }
}
