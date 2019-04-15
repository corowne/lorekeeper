<?php

namespace App\Models\Feature;

use Config;
use DB;
use App\Models\Model;
use App\Models\Feature\FeatureCategory;
use App\Models\Species;
use App\Models\Rarity;

class Feature extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'feature_category_id', 'species_id', 'rarity_id', 'name', 'has_image', 'description', 'parsed_description'
    ];
    protected $table = 'features';
    
    public static $createRules = [
        'feature_category_id' => 'nullable|exists:feature_categories,id',
        'species_id' => 'nullable|exists:specieses,id',
        'rarity_id' => 'required|exists:rarities,id',
        'name' => 'required|unique:features|between:3,25',
        'description' => 'nullable',
        'image' => 'mimes:png',
    ];
    
    public static $updateRules = [
        'feature_category_id' => 'nullable|exists:feature_categories,id',
        'species_id' => 'nullable|exists:specieses,id',
        'rarity_id' => 'required|exists:rarities,id',
        'name' => 'required|between:3,25',
        'description' => 'nullable',
        'image' => 'mimes:png',
    ];

    public function rarity() 
    {
        return $this->belongsTo('App\Models\Rarity');
    }
    
    public function species() 
    {
        return $this->belongsTo('App\Models\Species');
    }
    
    public function category() 
    {
        return $this->belongsTo('App\Models\Feature\FeatureCategory', 'feature_category_id');
    }

    public function scopeSortAlphabetical($query, $reverse = false)
    {
        return $query->orderBy('name', $reverse ? 'DESC' : 'ASC');
    }

    public function scopeSortCategory($query)
    {
        $ids = FeatureCategory::orderBy('sort', 'DESC')->pluck('id')->toArray();
        return $query->orderByRaw(DB::raw('FIELD(feature_category_id, '.implode(',', $ids).')'));
    }

    public function scopeSortSpecies($query)
    {
        $ids = Species::orderBy('sort', 'DESC')->pluck('id')->toArray();
        return $query->orderByRaw(DB::raw('FIELD(species_id, '.implode(',', $ids).')'));
    }
    
    public function scopeSortRarity($query, $reverse = false)
    {
        $ids = Rarity::orderBy('sort', $reverse ? 'ASC' : 'DESC')->pluck('id')->toArray();
        return $query->orderByRaw(DB::raw('FIELD(rarity_id, '.implode(',', $ids).')'));
    }

    public function scopeSortNewest($query)
    {
        return $query->orderBy('id', 'DESC');
    }

    public function scopeSortOldest($query)
    {
        return $query->orderBy('id');
    }

    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->url.'" class="display-trait">'.$this->name.'</a> (' . $this->rarity->displayName . ')';
    }

    public function getImageDirectoryAttribute()
    {
        return 'images/data/traits';
    }

    public function getImageFileNameAttribute()
    {
        return $this->id . '-image.png';
    }

    public function getImagePathAttribute()
    {
        return public_path($this->imageDirectory);
    }
    
    public function getImageUrlAttribute()
    {
        if (!$this->has_image) return null;
        return asset($this->imageDirectory . '/' . $this->imageFileName);
    }

    public function getUrlAttribute()
    {
        return url('world/traits?name='.$this->name);
    }

    public function getSearchUrlAttribute()
    {
        return url('characters?feature_id='.$this->id);
    }
}
