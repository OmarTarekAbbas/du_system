<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    //
    protected $fillable = ['MTBody','date','MTURL','ShortnedURL','IsysURL','IsysResponse','service_id','status','user_id','time'];

    public function service(){
        return $this->belongsTo('App\Service');
    }
}

//App\Message::create(['MTBody'=>'جابك الله','date'=>'2015-07-09','MTURL'=>'http://ivas.com.eg/IsysTemp/wap/11110','ShortnedURL'=>'http://s.ivas.info/71336','service_id'=>1,'status'=>1,'user_id'=>1]);
//App\Message::create(['MTBody'=>'حديث من فطر صائم','date'=>'2015-07-09','MTURL'=>'http://ivas.com.eg/IsysTemp/wap/11110','ShortnedURL'=>'http://s.ivas.info/192569','service_id'=>2,'status'=>1,'user_id'=>1]);