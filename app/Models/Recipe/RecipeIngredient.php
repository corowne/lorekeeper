<?php

namespace App\Models\Recipe;

use App;
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

    /**********************************************************************************************
    
        ACCESSORS

    **********************************************************************************************/

    /**
     * Gets the json decoded data array.
     *
     * @return string
     */
    public function getDataAttribute()
    {
        return json_decode($this->ingredient_data);
    }

    /**
     * Gets the associated ingredient item(s) or category(ies).
     *
     * @return string
     */
    public function getIngredientAttribute()
    {
        switch ($this->ingredient_type)
        {
            case 'Item':
                return App\Models\Item\Item::where('id', $this->data[0])->get()[0];
            case 'MultiItem':
                return App\Models\Item\Item::whereIn('id', $this->data)->get();
            case 'Category':
                return App\Models\Item\ItemCategory::where('id', $this->data[0])->get()[0];
            case 'MultiCategory':
                return App\Models\Item\ItemCategory::whereIn('id', $this->data)->get();
            case 'Currency':
                return App\Models\Currency\Currency::where('id', $this->data[0])->get()[0];
        }
        return null;
    }
}
