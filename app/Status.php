<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property string $created_at
 * @property string $updated_at
 * @property Charge[] $charges
 */
class Status extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'statues';

    /**
     * @var array
     */
    protected $fillable = ['title', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function charges()
    {
        return $this->hasMany('App\Charge', 'status_id');
    }
}
