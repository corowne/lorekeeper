<?php

namespace App\Models\Character;

use Config;
use DB;
use Carbon\Carbon;
use Notifications;
use App\Models\Model;

use App\Models\User\User;
use App\Models\Character\Character;
use App\Models\Character\CharacterCategory;
use App\Models\Item\Item;
use App\Models\Item\ItemLog;

class CharacterDrop extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'drop_id', 'character_id', 'parameters', 'drops_available'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_drops';

    /**
     * Dates on the model to convert to Carbon instances.
     *
     * @var array
     */
    public $dates = ['drops_available'];

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/
    
    /**
     * Get the associated character.
     */
    public function character() 
    {
        return $this->belongsTo('App\Models\Character\Character', 'character_id');
    }
    
    /**
     * Get the category the character belongs to.
     */
    public function dropData() 
    {
        return $this->belongsTo('App\Models\CharacterDropData', 'drop_id');
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
