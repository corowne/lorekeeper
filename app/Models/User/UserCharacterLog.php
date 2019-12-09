<?php

namespace App\Models\User;

use Config;
use App\Models\Model;

class UserCharacterLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_id', 'sender_id', 'recipient_id', 'recipient_alias',
        'log', 'log_type', 'data',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_character_log';

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
     * Displays the recipient's alias, linked to their deviantART page.
     *
     * @return string
     */
    public function getDisplayRecipientAliasAttribute()
    {
        return '<a href="http://www.deviantart.com/'.$this->recipient_alias.'">'.$this->recipient_alias.'@dA</a>';
    }

}
