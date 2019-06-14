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
    protected $table = 'user_character_log';
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
        return '<a href="http://www.deviantart.com/'.$this->recipient_alias.'">'.$this->recipient_alias.'@dA</a>';
    }

    public function displayRow($user) 
    {
        $ret = '<tr class="'.(($this->recipient_id == $user->id || $this->recipient_alias == $user->alias) ? 'inflow' : 'outflow').'">';
        $ret .= '<td>'.($this->sender ? $this->sender->displayName : '').'</td>';
        $ret .= '<td>'.($this->recipient ? $this->recipient->displayName : $this->displayRecipientAlias).'</td>';
        $ret .= '<td>'.$this->log.'</td>';
        $ret .= '<td>'.format_date($this->created_at).'</td>';
        return $ret . '</tr>';
    }

}
