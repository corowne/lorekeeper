<?php

namespace App\Models\Character;

use Config;
use DB;
use Auth;
use App\Models\Model;
use App\Models\Feature\FeatureCategory;

class CharacterLink extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id', 'child_id'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_links';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = false;
    
    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/
    
    /**
     * Get the parent associated with the link.
     */
    public function parent() 
    {
        return $this->belongsTo('App\Models\Character\Character', 'parent_id');
    }

    /**
     * Get the child associated with the link.
     */
    public function child() 
    {
        return $this->belongsTo('App\Models\Character\Character', 'child_id');
    }
    
    /**
     * Get the features (traits) attached to the child, ordered by display order.
     */
    public function features() 
    {
        $ids = FeatureCategory::orderBy('sort', 'DESC')->pluck('id')->toArray();

        $query = $this->hasMany('App\Models\Character\CharacterFeature', 'child_id')->where('character_features.character_type', 'Character')->join('features', 'features.id', '=', 'character_features.feature_id')->select(['character_features.*', 'features.*', 'character_features.id AS character_feature_id']);

        return count($ids) ? $query->orderByRaw(DB::raw('FIELD(features.feature_category_id, '.implode(',', $ids).')')) : $query;
    }
    
    /**********************************************************************************************
    
        SCOPES

    **********************************************************************************************/

    /**********************************************************************************************
    
        ACCESSORS

    **********************************************************************************************/
    
    /**
     * Displays the parent character's name, linked to their character page.
     *
     * @return string
     */
    public function getParentDisplayNameAttribute()
    {
        return '<a href="'.$this->parentUrl.'" class="display-character">'.$this->parentFullName.'</a>';
    }

    /**
     * Gets the character's name, including their code and user-assigned name.
     * If this is a MYO slot, simply returns the slot's name.
     *
     * @return string
     */
    public function getParentFullNameAttribute()
    {
        if($this->parent->is_myo_slot) return $this->parent->name;
        else return $this->parent->slug . ($this->parent->name ? ': '.$this->parent->name : '');
    }

    /**
     * Gets the character's page's URL.
     *
     * @return string
     */
    public function getParentUrlAttribute()
    {
        if($this->parent->is_myo_slot) return url('myo/'.$this->parent->id);
        else return url('character/'.$this->parent->slug);
    }

    /**
     * Displays the child character's name, linked to their character page.
     *
     * @return string
     */
    public function getChildDisplayNameAttribute()
    {
        return '<a href="'.$this->childUrl.'" class="display-character">'.$this->childFullName.'</a>';
    }

    /**
     * Gets the character's name, including their code and user-assigned name.
     * If this is a MYO slot, simply returns the slot's name.
     *
     * @return string
     */
    public function getChildFullNameAttribute()
    {
        if($this->child->is_myo_slot) return $this->child->name;
        else return $this->child->slug . ($this->child->name ? ': '.$this->child->name : '');
    }

    /**
     * Gets the character's page's URL.
     *
     * @return string
     */
    public function getChildUrlAttribute()
    {
        if($this->child->is_myo_slot) return url('myo/'.$this->child->id);
        else return url('character/'.$this->child->slug);
    }
}
