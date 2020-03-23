<?php

namespace App\Models\Species;

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

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'specieses';
    
    
    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required|unique:specieses|between:3,25',
        'description' => 'nullable',
        'image' => 'mimes:png',
    ];
    
    
    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => 'required|between:3,25',
        'description' => 'nullable',
        'image' => 'mimes:png',
    ];

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/

    /**
     * Get the subtypes for this species.
     */
    public function subtypes() 
    {
        return $this->hasMany('App\Models\Species\Subtype');
    }

    /**********************************************************************************************
    
        ACCESSORS

    **********************************************************************************************/
    
    /**
     * Displays the model's name, linked to its encyclopedia page.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->url.'" class="display-species">'.$this->name.'</a>';
    }

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute()
    {
        return 'images/data/species';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getSpeciesImageFileNameAttribute()
    {
        return $this->id . '-image.png';
    }

    /**
     * Gets the path to the file directory containing the model's image.
     *
     * @return string
     */
    public function getSpeciesImagePathAttribute()
    {
        return public_path($this->imageDirectory);
    }
    
    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getSpeciesImageUrlAttribute()
    {
        if (!$this->has_image) return null;
        return asset($this->imageDirectory . '/' . $this->speciesImageFileName);
    }

    /**
     * Gets the URL of the model's encyclopedia page.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('world/species?name='.$this->name);
    }

    /**
     * Gets the URL for a masterlist search of characters of this species.
     *
     * @return string
     */
    public function getSearchUrlAttribute()
    {
        return url('masterlist?species_id='.$this->id);
    }
}
