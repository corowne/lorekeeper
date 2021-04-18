<?php

namespace App\Models\Currency;

use Config;
use App\Models\Model;

class Currency extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_user_owned', 'is_character_owned', 
        'name', 'abbreviation', 'description', 'parsed_description', 'sort_user', 'sort_character',
        'is_displayed', 'allow_user_to_user', 'allow_user_to_character', 'allow_character_to_user',
        'has_icon', 'has_image'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'currencies';
    
    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required|unique:currencies|between:3,100',
        'abbreviation' => 'nullable|unique:currencies|between:1,25',
        'description' => 'nullable',
        'icon' => 'mimes:png',
        'image' => 'mimes:png'
    ];
    
    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => 'required|between:3,100',
        'abbreviation' => 'nullable|between:1,25',
        'description' => 'nullable',
        'icon' => 'mimes:png',
        'image' => 'mimes:png'
    ];

    /**********************************************************************************************
    
        ACCESSORS

    **********************************************************************************************/

    /**
     * Displays the currency as an icon with tooltip.
     *
     * @return string
     */
    public function getDisplayIconAttribute()
    {
        return '<img src="'.$this->currencyIconUrl.'" title="'.$this->name . ($this->abbreviation ? ' ('.$this->abbreviation.')' : '') .'" data-toggle="tooltip" />';
    }

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute()
    {
        return 'images/data/currencies';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getCurrencyImageFileNameAttribute()
    {
        return $this->id . '-image.png';
    }

    /**
     * Gets the file name of the model's icon image.
     *
     * @return string
     */
    public function getCurrencyIconFileNameAttribute()
    {
        return $this->id . '-icon.png';
    }

    /**
     * Gets the path to the file directory containing the model's image.
     *
     * @return string
     */
    public function getCurrencyImagePathAttribute()
    {
        return public_path($this->imageDirectory);
    }

    /**
     * Gets the path to the file directory containing the model's icon image.
     *
     * @return string
     */
    public function getCurrencyIconPathAttribute()
    {
        return public_path($this->imageDirectory);
    }
    
    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getCurrencyImageUrlAttribute()
    {
        if (!$this->has_image) return null;
        return asset($this->imageDirectory . '/' . $this->currencyImageFileName);
    }
    
    /**
     * Gets the URL of the model's icon image.
     *
     * @return string
     */
    public function getCurrencyIconUrlAttribute()
    {
        if (!$this->has_icon) return null;
        return asset($this->imageDirectory . '/' . $this->currencyIconFileName);
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
        return '<a href="'.$this->url.'" class="display-currency">'.$this->name.'</a>';
    }
    
    /**
     * Gets the URL of the model's encyclopedia page.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('world/currencies?name='.$this->name);
    }

    /**
     * Gets the currency's asset type for asset management.
     *
     * @return string
     */
    public function getAssetTypeAttribute()
    {
        return 'currencies';
    }

    /**********************************************************************************************
    
        OTHER FUNCTIONS

    **********************************************************************************************/

    /**
     * Displays a given value of the currency with icon, abbreviation or name.
     *
     * @return string
     */
    public function display($value) 
    {
        $ret = '<span class="display-currency">' . $value . ' ';
        if($this->has_icon) $ret .= $this->displayIcon;
        elseif ($this->abbreviation) $ret .= $this->abbreviation;
        else $ret .= $this->name;
        return $ret . '</span>';
    }
}
