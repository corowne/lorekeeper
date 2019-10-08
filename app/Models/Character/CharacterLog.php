<?php

namespace App\Models\Character;

use Config;
use App\Models\Model;

class CharacterLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_id', 'sender_id', 'recipient_id', 'recipient_alias',
        'log', 'log_type', 'data', 'change_log'
    ];
    protected $table = 'character_log';
    public $timestamps = true;

    public function sender() 
    {
        return $this->belongsTo('App\Models\User\User', 'sender_id');
    }

    public function recipient() 
    {
        return $this->belongsTo('App\Models\User\User', 'recipient_id');
    }

    public function character() 
    {
        return $this->belongsTo('App\Models\Character\Character');
    }

    public function getDisplayRecipientAliasAttribute()
    {
        if($this->recipient_alias)
            return '<a href="http://www.deviantart.com/'.$this->recipient_alias.'">'.$this->recipient_alias.'@dA</a>';
        else return '---';
    }

    public function getChangedDataAttribute()
    {
        return json_decode($this->change_log, true);
    }
}
