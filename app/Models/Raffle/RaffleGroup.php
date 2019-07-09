<?php namespace App\Models\Raffle;

use App\Models\Model;
use DB;

class RaffleGroup extends Model
{
    protected $table = 'raffle_groups';
    protected $fillable = [
        'name', 'is_active'
    ];
    public $timestamps = false; 

    /**************************************************************************
     *  RELATIONS
     **************************************************************************/
    public function raffles()
    {
        return $this->hasMany('App\Models\Raffle\Raffle', 'group_id')->orderBy('order');
    }
}
