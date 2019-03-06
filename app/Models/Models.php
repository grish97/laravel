<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Models extends Model
{
    protected $table = 'model';
    protected $fillable = ['name'];

    public function vehicle() {
        return $this->hasOne(Vehicle::class,'model_id','id');
    }
}
