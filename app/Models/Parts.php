<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parts extends Model
{
    protected $table = 'part';

    public function description() {
        return $this->hasOne(Descriptions::class,'id','description_id');
    }
}
