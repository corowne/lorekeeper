<?php

namespace App\Models\Feature;

use App\Models\Model;
use App\Models\Rarity;
use App\Models\Species\Species;
use DB;

class Feature extends Model
{
    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'feature_category_id' => 'nullable',
        'species_id'          => 'nullable',
        'subtype_id'          => 'nullable',
        'rarity_id'           => 'required|exists:rarities,id',
        'name'                => 'required|unique:features|between:3,100',
        'description'         => 'nullable',
        'image'               => 'mimes:png',
    ];

    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'feature_category_id' => 'nullable',
        'species_id'          => 'nullable',
        'subtype_id'          => 'nullable',
        'rarity_id'           => 'required|exists:rarities,id',
        'name'                => 'required|between:3,100',
        'description'         => 'nullable',
        'image'               => 'mimes:png',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'feature_category_id', 'species_id', 'subtype_id', 'rarity_id', 'name', 'has_image', 'description', 'parsed_description',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'features';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the rarity of this feature.
     */
    public function rarity()
    {
        return $this->belongsTo('App\Models\Rarity');
    }

    /**
     * Get the species the feature belongs to.
     */
    public function species()
    {
        return $this->belongsTo('App\Models\Species\Species');
    }

    /**
     * Get the subtype the feature belongs to.
     */
    public function subtype()
    {
        return $this->belongsTo('App\Models\Species\Subtype');
    }

    /**
     * Get the category the feature belongs to.
     */
    public function category()
    {
        return $this->belongsTo('App\Models\Feature\FeatureCategory', 'feature_category_id');
    }

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to sort features in alphabetical order.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param bool                                  $reverse
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortAlphabetical($query, $reverse = false)
    {
        return $query->orderBy('name', $reverse ? 'DESC' : 'ASC');
    }

    /**
     * Scope a query to sort features in category order.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortCategory($query)
    {
        if (FeatureCategory::all()->count()) {
            return $query->orderBy(FeatureCategory::select('sort')->whereColumn('features.feature_category_id', 'feature_categories.id'), 'DESC');
        }

        return $query;
    }

    /**
     * Scope a query to sort features in species order.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortSpecies($query)
    {
        $ids = Species::orderBy('sort', 'DESC')->pluck('id')->toArray();

        return count($ids) ? $query->orderByRaw(DB::raw('FIELD(species_id, '.implode(',', $ids).')')) : $query;
    }

    /**
     * Scope a query to sort features in rarity order.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param bool                                  $reverse
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortRarity($query, $reverse = false)
    {
        $ids = Rarity::orderBy('sort', $reverse ? 'ASC' : 'DESC')->pluck('id')->toArray();

        return count($ids) ? $query->orderByRaw(DB::raw('FIELD(rarity_id, '.implode(',', $ids).')')) : $query;
    }

    /**
     * Scope a query to sort features by newest first.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortNewest($query)
    {
        return $query->orderBy('id', 'DESC');
    }

    /**
     * Scope a query to sort features oldest first.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortOldest($query)
    {
        return $query->orderBy('id');
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
        return '<a href="'.$this->url.'" class="display-trait">'.$this->name.'</a>'.($this->rarity ? ' ('.$this->rarity->displayName.')' : '');
    }

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute()
    {
        return 'images/data/traits';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getImageFileNameAttribute()
    {
        return $this->id.'-image.png';
    }

    /**
     * Gets the path to the file directory containing the model's image.
     *
     * @return string
     */
    public function getImagePathAttribute()
    {
        return public_path($this->imageDirectory);
    }

    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        if (!$this->has_image) {
            return null;
        }

        return asset($this->imageDirectory.'/'.$this->imageFileName);
    }

    /**
     * Gets the URL of the model's encyclopedia page.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('world/traits?name='.$this->name);
    }

    /**
     * Gets the URL for a masterlist search of characters in this category.
     *
     * @return string
     */
    public function getSearchUrlAttribute()
    {
        return url('masterlist?feature_id[]='.$this->id);
    }
}
