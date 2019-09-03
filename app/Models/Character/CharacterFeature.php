<?php

namespace App\Models\Character;

use Config;
use DB;
use App\Models\Model;
use App\Models\Character\CharacterCategory;

class CharacterFeature extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_image_id', 'feature_id', 'data', 'character_type'
    ];
    protected $table = 'character_features';    
    
    public function image() 
    {
        return $this->belongsTo('App\Models\Character\CharacterImage', 'character_image_id');
    }
    
    public function feature() 
    {
        return $this->belongsTo('App\Models\Feature\Feature', 'feature_id');
    }
    
    //public function getDataAttribute()
    //{
    //    return json_decode($this->attributes['data'], true);
    //}
}
