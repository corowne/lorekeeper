<?php namespace App\Models\Raffle;

use App\Models\Model;
use DB;

class RaffleTicket extends Model
{
    protected $table = 'raffle_tickets';
    protected $fillable = [
        'user_id', 'raffle_id', 'position', 'created_at', 'alias'
    ];
    protected $dates = ['created_at'];

    /**************************************************************************
     *  SCOPES
     **************************************************************************/
    public function scopeWinners($q)
    {
        $q->whereNotNull('position')->orderBy('position');
    }

    /**************************************************************************
     *  RELATIONS
     **************************************************************************/
    public function raffle()
    {
        return $this->belongsTo('App\Models\Raffle\Raffle');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User\User');
    }
    public function getDisplayHolderNameAttribute()
    {
        if ($this->user_id) return $this->user->displayName;
        return '<a href="http://'.$this->alias.'.deviantart.com">'.$this->alias.'@dA</a>';
    }
}
