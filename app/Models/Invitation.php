<?php

namespace App\Models;

class Invitation extends Model
{
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
        'code', 'user_id', 'recipient_id',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invitations';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the user who generated the invitation code.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User\User');
    }

    /**
     * Get the user who created their account using the invitation code.
     */
    public function recipient()
    {
        return $this->belongsTo('App\Models\User\User', 'recipient_id');
    }
}
