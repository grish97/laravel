<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Models extends Model
{
    protected $table = 'model';
    protected $fillable = ['name'];

   public function make_model() {
       return $this->hasOne(Make_Models::class,'model_id','id');
   }
}
