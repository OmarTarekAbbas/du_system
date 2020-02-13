<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Operator extends Model
{
    //
    protected $fillable = ['title','channel','country_id'];

    public function country(){
        return $this->belongsTo('App\Country');
    }
    public function services(){
        return $this->hasMany('App\Service');
    }
}

//App\Operator::create(['title'=>'Zain','channel'=>'JORDAN','country_id'=>1]);