<?php

namespace App\Models;

use Config;
use App\Models\Model;

class SitePageCategory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'has_image', 'description', 'parsed_description', 'sort'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'site_page_categories';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = false;
    
    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required|unique:site_page_categories|between:3,25',
        'description' => 'nullable',
    ];
    
    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => 'required|between:3,25',
        'description' => 'nullable',
    ];

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
        return $this->name;
    }

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute()
    {
        return 'images/data/page-categories';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getCategoryImageFileNameAttribute()
    {
        return $this->id . '-image.png';
    }

    /**
     * Gets the path to the file directory containing the model's image.
     *
     * @return string
     */
    public function getCategoryImagePathAttribute()
    {
        return public_path($this->imageDirectory);
    }
    
    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getCategoryImageUrlAttribute()
    {
        if (!$this->has_image) return null;
        return asset($this->imageDirectory . '/' . $this->categoryImageFileName);
    }
}
