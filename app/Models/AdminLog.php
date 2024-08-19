<?php

namespace App\Models;

use App\Models\User\User;

class AdminLog extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'action', 'action_details',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin_log';
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

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the staff who preformed the action.
     */
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
