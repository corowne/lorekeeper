<?php namespace App\Models\Raffle;

use App\Models\Model;
use DB;

class Raffle extends Model
{
    protected $table = 'raffles';
    protected $fillable = [
        'name', 'is_active', 'winner_count', 'group_id', 'order'
    ];
    public $appends = ['name_with_group'];
    public $dates = ['rolled_at'];
    public $timestamps = false; 

    /**************************************************************************
     *  RELATIONS
     **************************************************************************/
    public function tickets()
    {
        return $this->hasMany('App\Models\Raffle\RaffleTicket');
    }
    
    public function group()
    {
        return $this->belongsTo('App\Models\Raffle\RaffleGroup', 'group_id');
    }

    public function getNameWithGroupAttribute()
    {
        return ($this->group_id ? '[' . $this->group->name . '] ' : '') . $this->name;
    }
}
