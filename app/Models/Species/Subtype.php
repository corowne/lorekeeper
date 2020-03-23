<?php

namespace App\Models\Species;

use Config;
use App\Models\Model;

class Subtype extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'species_id', 'name', 'sort', 'has_image', 'description', 'parsed_description'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'subtypes';
    
    
    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'species_id' => 'required',
        'name' => 'required|between:3,25',
        'description' => 'nullable',
        'image' => 'mimes:png',
    ];
    
    
    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'species_id' => 'required',
        'name' => 'required|between:3,25',
        'description' => 'nullable',
        'image' => 'mimes:png',
    ];
    
    /**
     * Accessors to append to the model.
     *
     * @var array
     */
    protected $appends = [
        'name_with_species'
    ];

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/
    
    /**
     * Get the species the subtype belongs to.
     */
    public function species() 
    {
        return $this->belongsTo('App\Models\Species\Species', 'species_id');
    }

    /**********************************************************************************************
    
        ACCESSORS

    **********************************************************************************************/

    /**
     * Displays the subtype's name and species.
     *
     * @return string
     */
    public function getNameWithSpeciesAttribute()
    {
        return $this->name . ' [' . $this->species->name . ' Subtype]';
    }
    
    /**
     * Displays the model's name, linked to its encyclopedia page.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->url.'" class="display-subtype">'.$this->name.'</a>';
    }

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute()
    {
        return 'images/data/subtypes';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getSubtypeImageFileNameAttribute()
    {
        return $this->id . '-image.png';
    }

    /**
     * Gets the path to the file directory containing the model's image.
     *
     * @return string
     */
    public function getSubtypeImagePathAttribute()
    {
        return public_path($this->imageDirectory);
    }
    
    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getSubtypeImageUrlAttribute()
    {
        if (!$this->has_image) return null;
        return asset($this->imageDirectory . '/' . $this->subtypeImageFileName);
    }

    /**
     * Gets the URL of the model's encyclopedia page.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('world/subtypes?name='.$this->name);
    }

    /**
     * Gets the URL for a masterlist search of characters of this species subtype.
     *
     * @return string
     */
    public function getSearchUrlAttribute()
    {
        return url('masterlist?subtype_id='.$this->id);
    }
}
