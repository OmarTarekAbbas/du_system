<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DuMo extends Model
{
    protected $fillable = ['link', 'msisdn', 'message'];
}
