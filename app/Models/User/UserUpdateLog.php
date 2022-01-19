<?php

namespace App\Models\User;

use App\Models\Model;

class UserUpdateLog extends Model
{
    /**
     * The primary key of the model.
     *
     * @var string
     */
    public $primaryKey = 'user_id';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'staff_id', 'user_id', 'data', 'type',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_update_log';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the staff who updated the user.
     */
    public function staff()
    {
        return $this->belongsTo('App\Models\User\User', 'staff_id');
    }

    /**
     * Get the user that was updated.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User\User', 'user_id');
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Get the data attribute as an associative array.
     *
     * @return array
     */
    public function getDataAttribute()
    {
        return json_decode($this->attributes['data'], true);
    }
}
