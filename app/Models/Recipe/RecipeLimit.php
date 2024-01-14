<?php

namespace App\Models\Recipe;

use App;
use Config;
use App\Models\Model;

class RecipeLimit extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'recipe_id', 'limit_type', 'limit_id', 'quantity'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'recipe_limits';

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/
    
    /**
     * Get the reward attached to the loot entry.
     */
    public function reward() 
    {
        switch ($this->limit_type)
        {
            case 'Item':
                return $this->belongsTo('App\Models\Item\Item', 'limit_id');
            case 'Currency':
                return $this->belongsTo('App\Models\Currency\Currency', 'limit_id');
            case 'Recipe':
                return $this->belongsTo('App\Models\Recipe\Recipe', 'limit_id');
            case 'None':
                // Laravel requires a relationship instance to be returned (cannot return null), so returning one that doesn't exist here.
                return $this->belongsTo('App\Models\Recipe\RecipeLimit', 'limit_id', 'recipe_id')->whereNull('recipe_id');
        }
        return null;
    }
}
