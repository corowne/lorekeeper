<?php

namespace App\Models\User;

use App\Models\Model;

class UserCharacterLog extends Model
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
        'character_id', 'sender_id', 'sender_alias', 'recipient_id', 'recipient_alias',
        'log', 'log_type', 'data', 'sender_url', 'recipient_url',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_character_log';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the user who initiated the logged action.
     */
    public function sender()
    {
        return $this->belongsTo('App\Models\User\User', 'sender_id');
    }

    /**
     * Get the user who received the logged action.
     */
    public function recipient()
    {
        return $this->belongsTo('App\Models\User\User', 'recipient_id');
    }

    /**
     * Get the character that is the target of the action.
     */
    public function character()
    {
        return $this->belongsTo('App\Models\Character\Character');
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Displays the sender's alias, linked to their profile.
     *
     * @return string
     */
    public function getDisplaySenderAliasAttribute()
    {
        return prettyProfileLink($this->sender_url);
    }

    /**
     * Displays the recipient's alias, linked to their profile.
     *
     * @return string
     */
    public function getDisplayRecipientAliasAttribute()
    {
        return prettyProfileLink($this->recipient_url);
    }
}
