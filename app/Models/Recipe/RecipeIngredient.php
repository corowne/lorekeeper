<?php

namespace App\Models\Recipe;

use Config;
use DB;
use App\Models\Model;

class RecipeIngredient extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'recipe_id', 'ingredient_type', 'ingredient_data', 'quantity'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'recipe_ingredients';
    
    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'recipe_id' => 'required',
        'ingredient_type' => 'required',
        'ingredient_data' => 'required',
        'quantity' => 'required|integer|min:1',
    ];
    
    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'recipe_id' => 'required',
        'ingredient_type' => 'required',
        'ingredient_data' => 'required',
        'quantity' => 'required|integer|min:1',
    ];

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/

    /**
     * Get the associated recipe.
     */
    public function recipe() 
    {
        return $this->belongsTo('App\Models\Recipe\Recipe');
    }

    /**
     * Gets the associated ingredient item(s) or category(ies).
     *
     * @return string
     */
    public function ingredient()
    {
        $ingredient_data = json_decode($this->ingredient_data);
        switch ($this->ingredient_type)
        {
            case 'Item':
                return App\Models\Item\Item::where('id', $ingredient_data['ids'][0]);
            case 'MultiItem':
                return App\Models\Item\Item::whereIn('id', $ingredient_data['ids']);
            case 'Category':
                return App\Models\Item\ItemCategory::where('id', $ingredient_data['ids'][0]);
            case 'MultiCategory':
                return App\Models\Item\ItemCategory::whereIn('id', $ingredient_data['ids']);
            case 'None':
                // Laravel requires a relationship instance to be returned (cannot return null), so returning one that doesn't exist here.
                return $this->belongsTo('App\Models\Recipe\RecipeIngredient', 'recipe_id', 'recipe_id')->whereNull('recipe_id');
        }
        return null;
    }
}
