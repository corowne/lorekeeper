<?php

namespace App\Models\Character;

use Config;
use App\Models\Model;

class CharacterCategory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'name', 'sort', 'has_image', 'description', 'parsed_description'
    ];
    protected $table = 'character_categories';
    
    public static $createRules = [
        'name' => 'required|unique:character_categories|between:3,25',
        'code' => 'required|unique:character_categories|between:1,25',
        'description' => 'nullable',
        'image' => 'mimes:png',
    ];
    
    public static $updateRules = [
        'name' => 'required|between:3,25',
        'code' => 'required|between:1,25',
        'description' => 'nullable',
        'image' => 'mimes:png',
    ];

    
    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->url.'" class="display-category">'.$this->name.' ('.$this->code.')</a>';
    }

    public function getImageDirectoryAttribute()
    {
        return 'images/data/character-categories';
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
        return url('world/character-categories?name='.$this->name);
    }

    public function getSearchUrlAttribute()
    {
        return url('world/characters?character_category_id='.$this->id);
    }
}
