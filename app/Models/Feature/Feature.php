<?php

namespace App\Models\Feature;

use Config;
use App\Models\Model;

class Feature extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id', 'name', 'has_image', 'description', 'parsed_description'
    ];
    protected $table = 'features';
    
    public static $createRules = [
        'category_id' => 'nullable|exists:feature_categories,id',
        'name' => 'required|unique:features|between:3,25',
        'description' => 'nullable',
        'image' => 'mimes:png',
    ];
    
    public static $updateRules = [
        'category_id' => 'nullable|exists:feature_categories,id',
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
}
