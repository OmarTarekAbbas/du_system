<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property integer $activation_id
 * @property string $created_at
 * @property string $updated_at
 * @property Activation $activation
 */
class Unsubscriber extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['activation_id', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function activation()
    {
        return $this->belongsTo('App\Activation');
    }
}
