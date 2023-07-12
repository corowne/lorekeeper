<?php

namespace App\Models\Shop;

use App\Models\Model;

class UserShopStock extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_shop_id', 'item_id', 'currency_id', 'cost', 'use_user_bank', 'use_character_bank', 'quantity','data', 'stock_type', 'is_visible',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_shop_stock';

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/
    /**
     * Get the data attribute as an associative array.
     *
     * @return array
     */
    public function getDataAttribute() 
    {
        return json_decode($this->attributes['data'], true);
    }
    
    /**
     * Checks if the stack is transferrable.
     *
     * @return array
     */
    public function getIsTransferrableAttribute()
    {
        if(!isset($this->data['disallow_transfer']) && $this->item->allow_transfer) return true;
        return false;
    }

    /**
     * Get the item being stocked.
     */
    public function item() 
    {
        return $this->belongsTo('App\Models\Item\Item');
    }
    
    /**
     * Get the shop that holds this item.
     */
    public function shop() 
    {
        return $this->belongsTo('App\Models\Shop\UserShop', 'user_shop_id');
    }
    
    /**
     * Get the currency the item must be purchased with.
     */
    public function currency() 
    {
        return $this->belongsTo('App\Models\Currency\Currency');
    }

     /**
     * Scopes active stock
     */
    public function scopeActive($query)
    {
        return $query->where('is_visible', 1);
    }

    /**
     * Makes the cost an integer for display
     */
    public function getDisplayCostAttribute()
    {
        return (int)$this->cost;
    }
}
