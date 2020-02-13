<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    //
    protected $fillable = ['path','fid'];
}
//App\Upload::create(['path'=>'Contents/1/09-07-2015/01 Gabak Allah.mp3','fid'=>11110]);