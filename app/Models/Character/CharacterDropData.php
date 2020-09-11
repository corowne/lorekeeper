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

class Character extends Model
{
    use SoftDeletes;

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
        'species_id' => 'required',
        'subtype_id' => 'nullable',
    ];
    
    /**
     * Validation rules for character updating.
     *
     * @var array
     */
    public static $updateRules = [
        'species_id' => 'required',
        'subtype_id' => 'nullable',
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
     * Get the subtype to which the data pertains, if set.
     */
    public function subtype() 
    {
        if($this->subtype_id) return $this->belongsTo('App\Models\Species\Subtype', 'subtype_id');
        else return null;
    }

    /**********************************************************************************************
    
        SCOPES

    **********************************************************************************************/

    /**********************************************************************************************
    
        ACCESSORS

    **********************************************************************************************/

    /**********************************************************************************************
    
        OTHER FUNCTIONS

    **********************************************************************************************/


}
