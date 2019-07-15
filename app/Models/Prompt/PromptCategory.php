<?php

namespace App\Models\Prompt;

use Config;
use App\Models\Model;

class PromptCategory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'sort', 'has_image', 'description', 'parsed_description'
    ];
    protected $table = 'prompt_categories';
    
    public static $createRules = [
        'name' => 'required|unique:prompt_categories|between:3,25',
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
        return 'images/data/prompt-categories';
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
        return url('world/prompt-categories?name='.$this->name);
    }

    public function getSearchUrlAttribute()
    {
        return url('world/prompts?prompt_category_id='.$this->id);
    }
}
