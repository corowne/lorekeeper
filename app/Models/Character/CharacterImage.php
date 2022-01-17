<?php

namespace App\Models\Character;

use Config;
use DB;
use App\Models\Model;
use App\Models\Feature\FeatureCategory;
use App\Models\Character\CharacterCategory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CharacterImage extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_id', 'user_id', 'species_id', 'subtype_id', 'rarity_id', 'url',
        'extension', 'use_cropper', 'hash', 'fullsize_hash', 'sort',
        'x0', 'x1', 'y0', 'y1',
        'description', 'parsed_description',
        'is_valid',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_images';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**
     * Validation rules for image creation.
     *
     * @var array
     */
    public static $createRules = [
        'species_id' => 'required',
        'rarity_id' => 'required',
        'image' => 'required|mimes:jpeg,jpg,gif,png|max:20000',
        'thumbnail' => 'nullable|mimes:jpeg,jpg,gif,png|max:20000',
    ];

    /**
     * Validation rules for image updating.
     *
     * @var array
     */
    public static $updateRules = [
        'character_id' => 'required',
        'user_id' => 'required',
        'species_id' => 'required',
        'rarity_id' => 'required',
        'description' => 'nullable',
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the character associated with the image.
     */
    public function character()
    {
        return $this->belongsTo('App\Models\Character\Character', 'character_id');
    }

    /**
     * Get the user who owned the character at the time of image creation.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User\User', 'user_id');
    }

    /**
     * Get the species of the character image.
     */
    public function species()
    {
        return $this->belongsTo('App\Models\Species\Species', 'species_id');
    }

    /**
     * Get the subtype of the character image.
     */
    public function subtype()
    {
        return $this->belongsTo('App\Models\Species\Subtype', 'subtype_id');
    }

    /**
     * Get the rarity of the character image.
     */
    public function rarity()
    {
        return $this->belongsTo('App\Models\Rarity', 'rarity_id');
    }

    /**
     * Get the features (traits) attached to the character image, ordered by display order.
     */
    public function features()
    {
        $ids = FeatureCategory::orderBy('sort', 'DESC')->pluck('id')->toArray();

        $query = $this->hasMany('App\Models\Character\CharacterFeature', 'character_image_id')->where('character_features.character_type', 'Character')->join('features', 'features.id', '=', 'character_features.feature_id')->select(['character_features.*', 'features.*', 'character_features.id AS character_feature_id']);

        return count($ids) ? $query->orderByRaw(DB::raw('FIELD(features.feature_category_id, '.implode(',', $ids).')')) : $query;
    }

    /**
     * Get the designers/artists attached to the character image.
     */
    public function creators()
    {
        return $this->hasMany('App\Models\Character\CharacterImageCreator', 'character_image_id');
    }

    /**
     * Get the designers attached to the character image.
     */
    public function designers()
    {
        return $this->hasMany('App\Models\Character\CharacterImageCreator', 'character_image_id')->where('type', 'Designer')->where('character_type', 'Character');
    }

    /**
     * Get the artists attached to the character image.
     */
    public function artists()
    {
        return $this->hasMany('App\Models\Character\CharacterImageCreator', 'character_image_id')->where('type', 'Artist')->where('character_type', 'Character');
    }

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to only include images visible to guests and regular logged-in users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeImages($query, $user = null)
    {
        if(!$user || !$user->hasPower('manage_characters')) return $query->where('is_visible', 1)->orderBy('sort')->orderBy('id', 'DESC');
        else return $query->orderBy('sort')->orderBy('id', 'DESC');
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute()
    {
        return 'images/characters/'.floor($this->id / 1000);
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getImageFileNameAttribute()
    {
        return $this->id . '_'.$this->hash.'.'.$this->extension;
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
        return asset($this->imageDirectory . '/' . $this->imageFileName);
    }

    /**
     * Gets the file name of the model's fullsize image.
     *
     * @return string
     */
    public function getFullsizeFileNameAttribute()
    {
        return $this->id . '_'.$this->hash.'_'.$this->fullsize_hash.'_full.'.$this->extension;
    }

    /**
     * Gets the file name of the model's fullsize image.
     *
     * @return string
     */
    public function getFullsizeUrlAttribute()
    {
        return asset($this->imageDirectory . '/' . $this->fullsizeFileName);
    }

    /**
     * Gets the file name of the model's fullsize image.
     *
     * @param  user
     * @return string
     */
    public function canViewFull($user = null)
    {
        if(((isset($this->character->user_id) && ($user ? $this->character->user->id == $user->id : false)) || ($user ? $user->hasPower('manage_characters') : false)))
        return true;
        else return false;
    }

    /**
     * Gets the file name of the model's thumbnail image.
     *
     * @return string
     */
    public function getThumbnailFileNameAttribute()
    {
        return $this->id . '_'.$this->hash.'_th.'.$this->extension;
    }

    /**
     * Gets the path to the file directory containing the model's thumbnail image.
     *
     * @return string
     */
    public function getThumbnailPathAttribute()
    {
        return $this->imagePath;
    }

    /**
     * Gets the URL of the model's thumbnail image.
     *
     * @return string
     */
    public function getThumbnailUrlAttribute()
    {
        return asset($this->imageDirectory . '/' . $this->thumbnailFileName);
    }
}
