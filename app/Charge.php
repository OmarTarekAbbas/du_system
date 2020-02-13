<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $subscriber_id
 * @property int $status_id
 * @property string $billing_request
 * @property string $billing_response
 * @property string $charging_date
 * @property string $created_at
 * @property string $updated_at
 * @property Statue $statue
 * @property Subscriber $subscriber
 */
class Charge extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['subscriber_id', 'status_id', 'billing_request', 'billing_response', 'charging_date', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function statue()
    {
        return $this->belongsTo('App\Statue', 'status_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscriber()
    {
        return $this->belongsTo('App\Subscriber');
    }
}
