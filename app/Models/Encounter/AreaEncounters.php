<?php

namespace App\Models\Encounter;

use Config;
use App\Models\Model;

class AreaEncounters extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'encounter_area_id', 'encounter_id','weight'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'area_encounters';

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'encounter_area_id' => 'required',
        'weight' => 'required|integer|min:1',
    ];

    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'encounter_area_id' => 'required',
        'weight' => 'required|integer|min:1',
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the reward attached to the loot entry.
     */
    public function encounter()
    {
        return $this->belongsTo('App\Models\Encounter\Encounter', 'encounter_id');
    }

     /**********************************************************************************************

        SCOPES

    **********************************************************************************************/
    

}