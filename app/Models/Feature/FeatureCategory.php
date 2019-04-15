<?php

namespace App\Models\Feature;

use Config;
use App\Models\Model;

class FeatureCategory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'sort', 'has_image', 'description', 'parsed_description'
    ];
    protected $table = 'feature_categories';
    
    public static $createRules = [
        'name' => 'required|unique:feature_categories|between:3,25',
        'description' => 'nullable',
        'image' => 'mimes:png',
    ];
    
    public static $updateRules = [
        'name' => 'required|between:3,25',
        'description' => 'nullable',
        'image' => 'mimes:png',
    ];

    
    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->url.'" class="display-category">'.$this->name.'</a>';
    }

    public function getImageDirectoryAttribute()
    {
        return 'images/data/trait-categories';
    }

    public function getCategoryImageFileNameAttribute()
    {
        return $this->id . '-image.png';
    }

    public function getCategoryImagePathAttribute()
    {
        return public_path($this->imageDirectory);
    }
    
    public function getCategoryImageUrlAttribute()
    {
        if (!$this->has_image) return null;
        return asset($this->imageDirectory . '/' . $this->categoryImageFileName);
    }

    public function getUrlAttribute()
    {
        return url('world/trait-categories?name='.$this->name);
    }

    public function getSearchUrlAttribute()
    {
        return url('world/traits?feature_category_id='.$this->id);
    }
}
