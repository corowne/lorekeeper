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
        'character_id', 'user_id', 'species_id', 'rarity_id', 'url',
        'extension', 'use_cropper', 'hash', 'sort', 
        'x0', 'x1', 'y0', 'y1',
        'description', 'parsed_description',
        'is_valid', 
    ];
    protected $table = 'character_images';
    public $timestamps = true;
    
    public static $createRules = [
        'species_id' => 'required',
        'rarity_id' => 'required',
        'image' => 'required|mimes:jpeg,gif,png|max:20000',
        'thumbnail' => 'nullable|mimes:jpeg,gif,png|max:20000',
    ];
    
    public static $updateRules = [
        'character_id' => 'required',
        'user_id' => 'required',
        'species_id' => 'required',
        'rarity_id' => 'required',
        'description' => 'nullable',
    ];
    
    
    public function character() 
    {
        return $this->belongsTo('App\Models\Character\Character', 'character_id');
    }
    
    public function user() 
    {
        return $this->belongsTo('App\Models\User\User', 'user_id');
    }
    
    public function species() 
    {
        return $this->belongsTo('App\Models\Species', 'species_id');
    }
    
    public function rarity() 
    {
        return $this->belongsTo('App\Models\Rarity', 'rarity_id');
    }

    public function features() 
    {
        $ids = FeatureCategory::orderBy('sort', 'DESC')->pluck('id')->toArray();

        return $this->hasMany('App\Models\Character\CharacterFeature', 'character_image_id')->where('character_features.character_type', 'Character')->join('features', 'features.id', '=', 'character_features.feature_id')->orderByRaw(DB::raw('FIELD(features.feature_category_id, '.implode(',', $ids).')'));
    }
    
    public function creators() 
    {
        return $this->hasMany('App\Models\Character\CharacterImageCreator', 'character_image_id');
    }
    
    public function designers() 
    {
        return $this->hasMany('App\Models\Character\CharacterImageCreator', 'character_image_id')->where('type', 'Designer')->where('character_type', 'Character');
    }
    
    public function artists() 
    {
        return $this->hasMany('App\Models\Character\CharacterImageCreator', 'character_image_id')->where('type', 'Artist')->where('character_type', 'Character');
    }

    public function scopeGuest($query)
    {
        return $query->where('is_visible', 1)->orderBy('sort')->orderBy('id', 'DESC');
    }

    public function scopeMod($query)
    {
        return $query->orderBy('sort')->orderBy('id', 'DESC');
    }
    
    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->url.'" class="display-character">'.$this->name.'</a>';
    }

    public function getImageDirectoryAttribute()
    {
        return 'images/characters/'.floor($this->id / 1000);
    }

    public function getImageFileNameAttribute()
    {
        return $this->id . '_'.$this->hash.'.'.$this->extension;
    }

    public function getImagePathAttribute()
    {
        return public_path($this->imageDirectory);
    }
    
    public function getImageUrlAttribute()
    {
        return asset($this->imageDirectory . '/' . $this->imageFileName);
    }

    public function getThumbnailFileNameAttribute()
    {
        return $this->id . '_'.$this->hash.'_th.'.$this->extension;
    }

    public function getThumbnailPathAttribute()
    {
        return $this->imagePath;
    }
    
    public function getThumbnailUrlAttribute()
    {
        return asset($this->imageDirectory . '/' . $this->thumbnailFileName);
    }

    public function getUrlAttribute()
    {
        return url('masterlist/'.$this->slug);
    }
}
