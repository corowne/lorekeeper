<?php

namespace App\Models\Character;

use Config;
use DB;
use Carbon\Carbon;
use Notifications;
use App\Models\Model;

use App\Models\Species\Species;
use App\Models\Species\Subtype;
use App\Models\Character\Character;
use App\Models\Currency\Currency;
use App\Models\Item\Item;

class CharacterDropData extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'species_id', 'subtype_id', 'parameters', 'data'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_drop_data';
    
    /**
     * Validation rules for character creation.
     *
     * @var array
     */
    public static $createRules = [
        'species_id' => 'required|unique:character_drops',
        'drop_frequency' => 'required',
        'drop_interval' => 'required'
    ];
    
    /**
     * Validation rules for character updating.
     *
     * @var array
     */
    public static $updateRules = [
        'species_id' => 'required',
        'drop_frequency' => 'required',
        'drop_interval' => 'required'
    ];

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/
    
    /**
     * Get the species to which the data pertains.
     */
    public function species() 
    {
        return $this->belongsTo('App\Models\Species\Species', 'species_id');
    }

    /**********************************************************************************************
    
        ACCESSORS

    **********************************************************************************************/

    /**
     * Get the parameter attribute as an associative array.
     *
     * @return array
     */
    public function getParametersAttribute()
    {
        return json_decode($this->attributes['parameters'], true);
    }

    /**
     * Get the parameter attribute as an associative array.
     *
     * @return array
     */
    public function getDataAttribute()
    {
        return json_decode($this->attributes['data'], true);
    }

}
