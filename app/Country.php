<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    //
    protected $fillable = ['name'];

    public function operators(){
        return $this->hasMany('App\Operator');
    }
}
//App\Country::create(['name'=>'Jordan']);