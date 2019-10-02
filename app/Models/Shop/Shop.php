<?php

namespace App\Models\Shop;

use Config;
use App\Models\Model;

class Shop extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'sort', 'has_image', 'description', 'parsed_description', 'is_active'
    ];
    protected $table = 'shops';
    
    public static $createRules = [
        'name' => 'required|unique:item_categories|between:3,25',
        'description' => 'nullable',
        'image' => 'mimes:png',
    ];
    
    public static $updateRules = [
        'name' => 'required|between:3,25',
        'description' => 'nullable',
        'image' => 'mimes:png',
    ];

    

    public function displayStock()
    {
        return $this->belongsToMany('App\Models\Item\Item', 'shop_stock')->withPivot('item_id', 'currency_id', 'cost', 'use_user_bank', 'use_character_bank', 'is_limited_stock', 'quantity', 'purchase_limit', 'id');
    }

    public function stock() 
    {
        return $this->hasMany('App\Models\Shop\ShopStock');
    }
    
    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->url.'" class="display-shop">'.$this->name.'</a>';
    }

    public function getImageDirectoryAttribute()
    {
        return 'images/data/shops';
    }

    public function getShopImageFileNameAttribute()
    {
        return $this->id . '-image.png';
    }

    public function getShopImagePathAttribute()
    {
        return public_path($this->imageDirectory);
    }
    
    public function getShopImageUrlAttribute()
    {
        if (!$this->has_image) return null;
        return asset($this->imageDirectory . '/' . $this->shopImageFileName);
    }

    public function getUrlAttribute()
    {
        return url('shops/'.$this->id);
    }
}
