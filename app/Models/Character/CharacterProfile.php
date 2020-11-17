<?php

namespace App\Models\Character;

use Config;
use DB;
use App\Models\Model;
use App\Models\Character\CharacterCategory;

class CharacterProfile extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_id', 'text', 'parsed_text', 'link'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_profiles';

    /**
     * The primary key of the model.
     *
     * @var string
     */
    public $primaryKey = 'character_id';

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/
    
    /**
     * Get the character this profile belongs to.
     */
    public function character() 
    {
        return $this->belongsTo('App\Models\Character\Character', 'character_id');
    }
}
