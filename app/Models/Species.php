<?php

namespace App\Models;

use Config;
use App\Models\Model;

class Species extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'sort', 'has_image', 'description', 'parsed_description'
    ];
    protected $table = 'specieses';
    
    public static $createRules = [
        'name' => 'required|unique:specieses|between:3,25',
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
        return '<a href="'.$this->url.'" class="display-species">'.$this->name.'</a>';
    }

    public function getImageDirectoryAttribute()
    {
        return 'images/data/species';
    }

    public function getSpeciesImageFileNameAttribute()
    {
        return $this->id . '-image.png';
    }

    public function getSpeciesImagePathAttribute()
    {
        return public_path($this->imageDirectory);
    }
    
    public function getSpeciesImageUrlAttribute()
    {
        if (!$this->has_image) return null;
        return asset($this->imageDirectory . '/' . $this->speciesImageFileName);
    }

    public function getUrlAttribute()
    {
        return url('world/species?name='.$this->name);
    }

    public function getSearchUrlAttribute()
    {
        return url('characters?species_id='.$this->id);
    }
}
