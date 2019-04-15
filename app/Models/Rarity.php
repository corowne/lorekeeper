<?php

namespace App\Models;

use Config;
use App\Models\Model;

class Rarity extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'sort', 'color', 'has_image', 'description', 'parsed_description'
    ];
    protected $table = 'rarities';
    
    public static $createRules = [
        'name' => 'required|unique:rarities|between:3,25',
        'color' => 'nullable|regex:/^#?[0-9a-fA-F]{6}$/i',
        'description' => 'nullable',
        'image' => 'mimes:png',
    ];
    
    public static $updateRules = [
        'name' => 'required|between:3,25',
        'color' => 'nullable|regex:/^#?[0-9a-fA-F]{6}$/i',
        'description' => 'nullable',
        'image' => 'mimes:png',
    ];

    
    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->url.'" class="display-rarity" '.($this->color ? 'style="color: #'.$this->color.';"' : '').'>'.$this->name.'</a>';
    }

    public function getImageDirectoryAttribute()
    {
        return 'images/data/rarities';
    }

    public function getRarityImageFileNameAttribute()
    {
        return $this->id . '-image.png';
    }

    public function getRarityImagePathAttribute()
    {
        return public_path($this->imageDirectory);
    }
    
    public function getRarityImageUrlAttribute()
    {
        if (!$this->has_image) return null;
        return asset($this->imageDirectory . '/' . $this->rarityImageFileName);
    }

    public function getUrlAttribute()
    {
        return url('world/rarities?name='.$this->name);
    }
    
    public function getSearchFeaturesUrlAttribute()
    {
        return url('world/traits?rarity_id='.$this->id);
    }
    
    public function getSearchCharactersUrlAttribute()
    {
        return url('characters?rarity_id='.$this->id);
    }
}
