<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleParts extends Model
{
    protected $table = 'vehicle_part';

    public function vehicle() {
        return $this->hasOne(Vehicle::class,'id','vehicle_id')->orderBy('year','desc');
    }
}
