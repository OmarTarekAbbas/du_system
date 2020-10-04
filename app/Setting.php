<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Setting.
 *
 * @author  The scaffold-interface created at 2017-04-10 09:12:38am
 * @link  https://github.com/amranidev/scaffold-interface
 */
class Setting extends Model
{

    protected $table = 'settings';
    protected $fillable = ['key','value'];


}
