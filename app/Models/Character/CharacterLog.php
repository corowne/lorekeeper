<?php

namespace App\Models\Character;

use App\Models\Model;

class CharacterLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_id', 'sender_id', 'sender_alias', 'recipient_id', 'recipient_alias',
        'log', 'log_type', 'data', 'change_log', 'sender_url', 'recipient_url',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_log';
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
     * Displays the recipient's alias if applicable.
     *
     * @return string
     */
    public function getDisplayRecipientAliasAttribute()
    {
        if ($this->recipient_url) {
            return prettyProfileLink($this->recipient_url);
        } else {
            return '---';
        }
    }

    /**
     * Retrieves the changed data as an associative array.
     *
     * @return array
     */
    public function getChangedDataAttribute()
    {
        return json_decode($this->change_log, true);
    }
}
