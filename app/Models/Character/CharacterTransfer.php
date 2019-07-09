<?php

namespace App\Models\Character;

use Config;
use Settings;
use App\Models\Model;

class CharacterTransfer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_id', 'sender_id', 'recipient_id', 
        'status', 'is_approved', 'reason', 'data'
    ];
    protected $table = 'character_transfers';
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

    public function scopeActive($query)
    {
        $query->where('status', 'Pending');

        if(Settings::get('open_transfers_queue')) {
            $query->orWhere(function($query) {
                $query->where('status', 'Accepted')->where('is_approved', 0);
            });
        }

        return $query;
    }

    public function scopeCompleted($query)
    {
        $query->where('status', 'Rejected')->orWhere('status', 'Canceled')->orWhere(function($query) {
            $query->where('status', 'Accepted')->where('is_approved', 1);
        });;
        return $query;
    }

    public function getIsActiveAttribute()
    {
        if($this->status == 'Pending') return true;

        if(Settings::get('open_transfers_queue')) {
            if($this->status == 'Accepted' && $this->is_approved == 0) return true;
        }

        return false;
    }

    public function getDataAttribute()
    {
        return json_decode($this->attributes['data'], true);
    }
}
