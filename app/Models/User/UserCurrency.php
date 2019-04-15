<?php

namespace App\Models\User;

use App\Models\Model;

class UserCurrency extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'quantity'
    ];
    //public $primaryKey = 'user_id';
    protected $table = 'user_currencies';

    public function user() 
    {
        return $this->belongsTo('App\Models\User\User');
    }
    
    public function currency() 
    {
        return $this->belongsTo('App\Models\Currency\Currency');
    }
}
