<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogMessage extends Model
{
    protected $fillable = ['service','msisdn','message','message_type'];


}
