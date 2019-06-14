<?php

namespace App\Models\Character;

use App\Models\Model;

class CharacterCurrency extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'quantity', 'character_id', 'currency_id'
    ];
    //public $primaryKey = 'user_id';
    protected $table = 'character_currencies';

    public function character() 
    {
        return $this->belongsTo('App\Models\Character\Character');
    }
    
    public function currency() 
    {
        return $this->belongsTo('App\Models\Currency\Currency');
    }
}
