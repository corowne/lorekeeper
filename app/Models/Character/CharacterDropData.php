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
        'species_id', 'subtype_id', 'parameters', 'data', 'is_active'
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
        'species_id' => 'required|unique:character_drop_data',
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

    /**
     * Get any character drops using this data.
     */
    public function characterDrops()
    {
        return $this->hasMany('App\Models\Character\CharacterDrop', 'drop_id');
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Get the parameter attribute as an associative array.
     *
     * @return array
     */
    public function getUrlAttribute()
    {
        return url('admin/data/character-drops/edit/'.$this->id);
    }

    /**
     * Get the parameter attribute as an associative array.
     *
     * @return array
     */
    public function getParametersAttribute()
    {
        if(isset($this->attributes['parameters'])) return json_decode($this->attributes['parameters'], true);
        else return null;
    }

    /**
     * Get the parameter attribute as an array with the keys and values the same.
     *
     * @return array
     */
    public function getParameterArrayAttribute()
    {
        foreach($this->parameters as $parameter=>$weight) $paramArray[$parameter] = $parameter;
        return $paramArray;
    }

    /**
     * Get the parameter attribute as an associative array.
     *
     * @return array
     */
    public function getDataAttribute()
    {
        if(isset($this->attributes['data'])) return json_decode($this->attributes['data'], true);
        else return null;
    }

    /**
     * Check if the drop data is active or not.
     *
     * @return array
     */
    public function getIsActiveAttribute()
    {
        return $this->attributes['is_active'];
    }

    /**
     * Retrieve the drop data's cap.
     *
     * @return array
     */
    public function getCapAttribute()
    {
        return isset($this->data['cap']) ? $this->data['cap'] : null;
    }

    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/

    /**
     * Rolls a group for a character.
     *
     * @return string
     */
    public function rollParameters()
    {
        $parameters = $this->parameters;
        $totalWeight = 0;
        foreach($parameters as $parameter=>$weight) $totalWeight += $weight;

        for($i = 0; $i < 1; $i++)
        {
            $roll = mt_rand(0, $totalWeight - 1);
            $result = null;
            $prev = null;
            $count = 0;
            foreach($parameters as $parameter=>$weight)
            {
                $count += $weight;

                if($roll < $count)
                {
                    $result = $parameter;
                    break;
                }
                $prev = $parameter;
            }
            if(!$result) $result = $prev;
        }
        return $result;
    }

}
