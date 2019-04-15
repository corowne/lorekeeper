<?php

namespace App\Models\Item;

use Config;
use DB;
use App\Models\Model;
use App\Models\Item\ItemCategory;

class Item extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_category_id', 'name', 'has_image', 'description', 'parsed_description'
    ];
    protected $table = 'items';
    
    public static $createRules = [
        'item_category_id' => 'nullable|exists:item_categories,id',
        'name' => 'required|unique:items|between:3,25',
        'description' => 'nullable',
        'image' => 'mimes:png',
    ];
    
    public static $updateRules = [
        'item_category_id' => 'nullable|exists:item_categories,id',
        'name' => 'required|between:3,25',
        'description' => 'nullable',
        'image' => 'mimes:png',
    ];
    
    
    public function category() 
    {
        return $this->belongsTo('App\Models\Item\ItemCategory', 'item_category_id');
    }

    public function scopeSortAlphabetical($query, $reverse = false)
    {
        return $query->orderBy('name', $reverse ? 'DESC' : 'ASC');
    }

    public function scopeSortCategory($query)
    {
        $ids = ItemCategory::orderBy('sort', 'DESC')->pluck('id')->toArray();
        return $query->orderByRaw(DB::raw('FIELD(item_category_id, '.implode(',', $ids).')'));
    }

    public function scopeSortNewest($query)
    {
        return $query->orderBy('id', 'DESC');
    }

    public function scopeSortOldest($query)
    {
        return $query->orderBy('id');
    }
    
    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->url.'" class="display-category">'.$this->name.'</a>';
    }

    public function getImageDirectoryAttribute()
    {
        return 'images/data/items';
    }

    public function getImageFileNameAttribute()
    {
        return $this->id . '-image.png';
    }

    public function getImagePathAttribute()
    {
        return public_path($this->imageDirectory);
    }
    
    public function getImageUrlAttribute()
    {
        if (!$this->has_image) return null;
        return asset($this->imageDirectory . '/' . $this->imageFileName);
    }

    public function getUrlAttribute()
    {
        return url('world/item-categories?name='.$this->name);
    }
}
