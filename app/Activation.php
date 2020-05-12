<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $trxid
 * @property string $msisdn
 * @property string $serviceid
 * @property string $plan
 * @property string $price
 * @property string $created_at
 * @property string $updated_at
 * @property string $du_request
 * @property string $du_response
 * @property string $status_code
 * @property Subscriber[] $subscribers
 * @property Unsubscriber[] $unsubscribers
 */
class Activation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'activation';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['trxid', 'msisdn', 'serviceid', 'plan', 'price', 'created_at', 'updated_at', 'du_request', 'du_response', 'status_code'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscribers()
    {
        return $this->hasMany('App\Subscriber');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function unsubscribers()
    {
        return $this->hasMany('App\Unsubscriber');
    }
}
