<?php

namespace App\Models\Stat;

use Config;
use App\Models\Model;

class Stat extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'abbreviation', 'base', 'step', 'multiplier', 'max_level'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stats';
    
    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required|unique:stats|between:3,25',
    ];
    
    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => 'required|between:3,25',
    ];

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/

    /**
     * Get the shop stock.
     */
    public function stock() 
    {
        return $this->hasMany('App\Models\Shop\ShopStock');
    }
    
}