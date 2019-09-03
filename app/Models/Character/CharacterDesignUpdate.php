<?php

namespace App\Models\Character;

use Config;
use DB;
use App\Models\Model;
use App\Models\Currency\Currency;
use App\Models\Feature\FeatureCategory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CharacterDesignupdate extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_id', 'status', 'user_id', 'staff_id',
        'comments', 'staff_comments', 'data', 'extension',
        'use_cropper', 'x0', 'x1', 'y0', 'y1',
        'hash', 'species_id', 'rarity_id', 
        'has_comments', 'has_image', 'has_addons', 'has_features'
    ];
    protected $table = 'design_updates';
    public $timestamps = true;
    
    public static $imageRules = [
        'image' => 'nullable|mimes:jpeg,gif,png',
        'thumbnail' => 'nullable|mimes:jpeg,gif,png',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'Approved')->where('status', '!=', 'Rejected');
    }
    
    public function character() 
    {
        return $this->belongsTo('App\Models\Character\Character', 'character_id');
    }
    
    public function user() 
    {
        return $this->belongsTo('App\Models\User\User', 'user_id');
    }
    public function staff() 
    {
        return $this->belongsTo('App\Models\User\User', 'staff_id');
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

        return $this->hasMany('App\Models\Character\CharacterFeature', 'character_image_id')->where('character_features.character_type', 'Update')->join('features', 'features.id', '=', 'character_features.feature_id')->orderByRaw(DB::raw('FIELD(features.feature_category_id, '.implode(',', $ids).')'));
    }
    
    public function designers() 
    {
        return $this->hasMany('App\Models\Character\CharacterImageCreator', 'character_image_id')->where('type', 'Designer')->where('character_type', 'Update');
    }
    
    public function artists() 
    {
        return $this->hasMany('App\Models\Character\CharacterImageCreator', 'character_image_id')->where('type', 'Artist')->where('character_type', 'Update');
    }

    public function getDataAttribute()
    {
        return json_decode($this->attributes['data'], true);
    }

    public function getInventoryAttribute()
    {
        // This is for showing the addons page
        // just need to retrieve a list of stack IDs to tell which ones to check

        return $this->data ? $this->data['stacks'] : [];
    }

    public function getUserBankAttribute()
    {
        return $this->data && isset($this->data['user']['currencies']) ? $this->data['user']['currencies'] : [];
    }

    public function getBank($type)
    {
        if($type == 'user') $currencies = $this->userBank;
        else $currencies = $this->characterBank;
        if(!count($currencies)) return [];
        $ids = array_keys($currencies);
        $result = Currency::whereIn('id', $ids)->get();
        foreach($result as $i=>$currency)
        {
            $currency->quantity = $currencies[$currency->id];
        }
        return $result;
    }

    public function getCharacterBankAttribute()
    {
        return $this->data && isset($this->data['character']['currencies']) ? $this->data['character']['currencies'] : [];
    }

    public function getIsCompleteAttribute()
    {
        // Quick check to find out if all sections of the form have been touched
        return ($this->has_comments && $this->has_image && $this->has_addons && $this->has_features);
    }

    public function getImageDirectoryAttribute()
    {
        return 'images/character-updates/'.floor($this->id / 1000);
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
        return url('designs/'.$this->id);
    }
}
