<?php

namespace App\Models;

use Config;
use Settings;

use App\Models\Character\Character;

use App\Models\Model;

class Trade extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sender_id', 'recipient_id', 'comments',
        'status', 'is_sender_confirmed', 'is_recipient_confirmed', 'is_sender_trade_confirmed', 'is_recipient_trade_confirmed',
        'is_approved', 'reason', 'data'
    ];
    protected $table = 'trades';
    public $timestamps = true;

    public function sender() 
    {
        return $this->belongsTo('App\Models\User\User', 'sender_id');
    }

    public function recipient() 
    {
        return $this->belongsTo('App\Models\User\User', 'recipient_id');
    }

    public function staff() 
    {
        return $this->belongsTo('App\Models\User\User', 'staff_id');
    }

    public function getIsActiveAttribute()
    {
        if($this->status == 'Pending') return true;

        if(Settings::get('open_transfers_queue')) {
            if($this->status == 'Accepted' && $this->is_approved == 0) return true;
        }

        return false;
    }

    public function getIsConfirmableAttribute()
    {
        if($this->is_sender_confirmed && $this->is_recipient_confirmed) return true;
        return false;
    }

    public function getDataAttribute()
    {
        return json_decode($this->attributes['data'], true);
    }

    public function getUrlAttribute()
    {
        return url('trades/'.$this->id);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed')->orWhere('status', 'Rejected');
    }

    public function getCharacterData()
    {
        return Character::whereIn('id', array_merge($this->getCharacters($this->sender), $this->getCharacters($this->recipient)))->get();
    }

    public function getInventory($user)
    {
        $type = $this->sender_id == $user->id ? 'sender' : 'recipient';
        $inventory = $this->data && isset($this->data[$type]) && isset($this->data[$type]['user_items']) ? $this->data[$type]['user_items'] : [];
        if($inventory) $inventory = array_keys($inventory);
        return $inventory;
    }

    public function getCharacters($user)
    {
        $type = $this->sender_id == $user->id ? 'sender' : 'recipient';
        $characters = $this->data && isset($this->data[$type]) && isset($this->data[$type]['characters']) ? $this->data[$type]['characters'] : [];
        if($characters) $characters = array_keys($characters);
        return $characters;
    }

    public function getCurrencies($user)
    {
        $type = $this->sender_id == $user->id ? 'sender' : 'recipient';
        return $this->data && isset($this->data[$type]) && isset($this->data[$type]['currencies']) ? $this->data[$type]['currencies'] : [];
    }
}
