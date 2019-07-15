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
    protected $table = 'currencies';
    
    public static $createRules = [
        'name' => 'required|unique:currencies|between:3,25',
        'abbreviation' => 'nullable|unique:currencies|between:1,25',
        'description' => 'nullable',
        'icon' => 'mimes:png',
        'image' => 'mimes:png'
    ];
    
    public static $updateRules = [
        'name' => 'required|between:3,25',
        'abbreviation' => 'nullable|between:1,25',
        'description' => 'nullable',
        'icon' => 'mimes:png',
        'image' => 'mimes:png'
    ];

    public function display($value) 
    {
        $ret = '<span class="display-currency">' . $value . ' ';
        if($this->has_icon) $ret .= $this->displayIcon;
        elseif ($this->abbreviation) $ret .= $this->abbreviation;
        else $ret .= $this->name;
        return $ret . '</span>';
    }

    public function getDisplayIconAttribute()
    {
        return '<img src="'.$this->currencyIconUrl.'" title="'.$this->name . ($this->abbreviation ? ' ('.$this->abbreviation.')' : '') .'" data-toggle="tooltip" />';
    }

    public function getImageDirectoryAttribute()
    {
        return 'images/data/currencies';
    }

    public function getCurrencyImageFileNameAttribute()
    {
        return $this->id . '-image.png';
    }

    public function getCurrencyIconFileNameAttribute()
    {
        return $this->id . '-icon.png';
    }

    public function getCurrencyImagePathAttribute()
    {
        return public_path($this->imageDirectory);
    }

    public function getCurrencyIconPathAttribute()
    {
        return public_path($this->imageDirectory);
    }
    
    public function getCurrencyImageUrlAttribute()
    {
        if (!$this->has_image) return null;
        return asset($this->imageDirectory . '/' . $this->currencyImageFileName);
    }

    public function getCurrencyIconUrlAttribute()
    {
        if (!$this->has_image) return null;
        return asset($this->imageDirectory . '/' . $this->currencyIconFileName);
    }
    
    public function getUrlAttribute()
    {
        return url('world/currencies?name='.$this->name);
    }
    
    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->url.'" class="display-currency">'.$this->name.'</a>';
    }

    public function getAssetTypeAttribute()
    {
        return 'currencies';
    }
}
