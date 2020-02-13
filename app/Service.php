<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    //
    protected $fillable = ['title','service','lang','type','operator_id','size','ExURL','service_id','sender_name'];

    public function operator(){
        return $this->belongsTo('App\Operator');
    }

    public function messages(){
        return $this->hasMany('App\Message');
    }
}

//App\Service::create(['title'=>'Al Afasy','service'=>'HamedZaid','lang'=>'ar','type'=>'SMS','operator_id'=>1]);
//App\Service::create(['title'=>'Afasy MMS','service'=>'AFASYMMS','lang'=>'ar','type'=>'MMS','operator_id'=>1]);
