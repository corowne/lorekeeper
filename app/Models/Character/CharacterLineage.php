<?php

namespace App\Models\Character;

use App\Models\Model;

use App\Models\Character\Character;

class CharacterLineage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_id',
        'sire_id',              'sire_name',
        'sire_sire_id',         'sire_sire_name',
        'sire_sire_sire_id',    'sire_sire_sire_name',
        'sire_sire_dam_id',     'sire_sire_dam_name',
        'sire_dam_id',          'sire_dam_name',
        'sire_dam_sire_id',     'sire_dam_sire_name',
        'sire_dam_dam_id',      'sire_dam_dam_name',
        'dam_id',               'dam_name',
        'dam_sire_id',          'dam_sire_name',
        'dam_sire_sire_id',     'dam_sire_sire_name',
        'dam_sire_dam_id',      'dam_sire_dam_name',
        'dam_dam_id',           'dam_dam_name',
        'dam_dam_sire_id',      'dam_dam_sire_name',
        'dam_dam_dam_id',       'dam_dam_dam_name',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_lineages';

    // test
    private $unknown = "Unknown";

    /*
     * ASSOCIATING THE FAMILY CHARACTER MODELS
     */

    public function sire()
    {
        return $this->belongsTo('App\Models\Character\Character');
    }

    public function sire_sire()
    {
        return $this->belongsTo('App\Models\Character\Character');
    }

    public function sire_sire_sire()
    {
        return $this->belongsTo('App\Models\Character\Character');
    }

    public function sire_sire_dam()
    {
        return $this->belongsTo('App\Models\Character\Character');
    }

    public function sire_dam()
    {
        return $this->belongsTo('App\Models\Character\Character');
    }

    public function sire_dam_sire()
    {
        return $this->belongsTo('App\Models\Character\Character');
    }

    public function sire_dam_dam()
    {
        return $this->belongsTo('App\Models\Character\Character');
    }

    public function dam()
    {
        return $this->belongsTo('App\Models\Character\Character');
    }

    public function dam_sire()
    {
        return $this->belongsTo('App\Models\Character\Character');
    }

    public function dam_sire_sire()
    {
        return $this->belongsTo('App\Models\Character\Character');
    }

    public function dam_sire_dam()
    {
        return $this->belongsTo('App\Models\Character\Character');
    }

    public function dam_dam()
    {
        return $this->belongsTo('App\Models\Character\Character');
    }

    public function dam_dam_sire()
    {
        return $this->belongsTo('App\Models\Character\Character');
    }

    public function dam_dam_dam()
    {
        return $this->belongsTo('App\Models\Character\Character');
    }

    /*
     * Getting the Display URLs or Text
     */

    public function getDisplaySire()
    {
        if(isset($this->sire_id))               return $this->sire->getDisplayNameAttribute();
        if(isset($this->sire_name))             return $this->sire_name;
        return "Unknown";
    }

    public function getDisplaySireSire()
    {
       if(isset($this->sire_sire_id))           return $this->sire_sire->getDisplayNameAttribute();
       if(isset($this->sire_sire_name))         return $this->sire_sire_name;
       return "Unknown";
    }

    public function getDisplaySireSireSire()
    {
       if(isset($this->sire_sire_sire_id))      return $this->sire_sire_sire->getDisplayNameAttribute();
       if(isset($this->sire_sire_sire_name))    return $this->sire_sire_sire_name;
       return "Unknown";
    }

    public function getDisplaySireSireDam()
    {
       if(isset($this->sire_sire_dam_id))       return $this->sire_sire_dam->getDisplayNameAttribute();
       if(isset($this->sire_sire_dam_name))     return $this->sire_sire_dam_name;
       return "Unknown";
    }

    public function getDisplaySireDam()
    {
       if(isset($this->sire_dam_id))            return $this->sire_dam->getDisplayNameAttribute();
       if(isset($this->sire_dam_name))          return $this->sire_dam_name;
       return "Unknown";
    }

    public function getDisplaySireDamSire()
    {
       if(isset($this->sire_dam_sire_id))        return $this->sire_dam_sire->getDisplayNameAttribute();
       if(isset($this->sire_dam_sire_name))      return $this->sire_dam_sire_name;
       return "Unknown";
    }

    public function getDisplaySireDamDam()
    {
       if(isset($this->sire_dam_dam_id))         return $this->sire_dam_dam->getDisplayNameAttribute();
       if(isset($this->sire_dam_dam_name))       return $this->sire_dam_dam_name;
       return "Unknown";
    }

    public function getDisplayDam()
    {
        if(isset($this->dam_id))               return $this->dam->getDisplayNameAttribute();
        if(isset($this->dam_name))             return $this->dam_name;
        return "Unknown";
    }

    public function getDisplayDamSire()
    {
       if(isset($this->dam_sire_id))           return $this->dam_sire->getDisplayNameAttribute();
       if(isset($this->dam_sire_name))         return $this->dam_sire_name;
       return "Unknown";
    }

    public function getDisplayDamSireSire()
    {
       if(isset($this->dam_sire_sire_id))      return $this->dam_sire_sire->getDisplayNameAttribute();
       if(isset($this->dam_sire_sire_name))    return $this->dam_sire_sire_name;
       return "Unknown";
    }

    public function getDisplayDamSireDam()
    {
       if(isset($this->dam_sire_dam_id))       return $this->dam_sire_dam->getDisplayNameAttribute();
       if(isset($this->dam_sire_dam_name))     return $this->dam_sire_dam_name;
       return "Unknown";
    }

    public function getDisplayDamDam()
    {
       if(isset($this->dam_dam_id))            return $this->dam_dam->getDisplayNameAttribute();
       if(isset($this->dam_dam_name))          return $this->dam_dam_name;
       return "Unknown";
    }

    public function getDisplayDamDamSire()
    {
       if(isset($this->dam_dam_sire_id))        return $this->dam_dam_sire->getDisplayNameAttribute();
       if(isset($this->dam_dam_sire_name))      return $this->dam_dam_sire_name;
       return "Unknown";
    }

    public function getDisplayDamDamDam()
    {
       if(isset($this->dam_dam_dam_id))         return $this->dam_dam_dam->getDisplayNameAttribute();
       if(isset($this->dam_dam_dam_name))       return $this->dam_dam_dam_name;
       return "Unknown";
    }

    /*
     * TEST
     */
    //

    //




    /**
     * Does this character have a sire?
     *
     * @return boolean
     */
    public function hasSire()
    {
        return isset($this->sire_id) || isset($this->sire_name);
    }

    /**
     * Gets the Sire's ID or Name?
     *
     * @return integer/string
     */
    public function getSire()
    {
        return $this->hasSire() ? (isset($this->sire_id) ? $this->sire_id : $this->sire_name) : $unknown;
    }

    /**
     * Does this character have a dam?
     *
     * @return boolean
     */
    public function hasDam()
    {
        return isset($this->dam_id) || isset($this->dam_name);
    }
}
