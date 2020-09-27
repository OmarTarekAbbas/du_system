<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property integer $activation_id
 * @property string $next_charging_date
 * @property string $subscribe_date
 * @property boolean $final_status
 * @property boolean $charging_cron
 * @property string $created_at
 * @property string $updated_at
 * @property Activation $activation
 * @property Charge[] $charges
 */
class Subscriber extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['activation_id', 'next_charging_date', 'subscribe_date', 'final_status', 'charging_cron', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function activation()
    {
        return $this->belongsTo('App\Activation');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function charges()
    {
        return $this->hasMany('App\Charge');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mts()
    {
        return $this->hasMany('App\LogMessage', 'msisdn', 'msisdn');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mos()
    {
        return $this->hasMany('App\DuMo', 'msisdn', 'msisdn');
    }
}
