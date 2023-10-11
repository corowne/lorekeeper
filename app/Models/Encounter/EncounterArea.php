<?php

namespace App\Models\Encounter;

use Config;
use DB;
use Carbon\Carbon;
use App\Models\Model;
use App\Models\Encounter\EncounterReward;

class EncounterArea extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','description','parsed_description', 'is_active','has_image', 'start_at', 'end_at'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'encounter_areas';

    /**
     * Validation rules for character creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required|between:3,100',
    ];

    /**
     * Validation rules for character updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => 'required|between:3,100',
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the loot data for this loot table.
     */
    public function encounters()
    {
        return $this->hasMany('App\Models\Encounter\AreaEncounters', 'encounter_area_id');
    }


    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to sort encounters in alphabetical order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  bool                                   $reverse
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortAlphabetical($query, $reverse = false)
    {
        return $query->orderBy('name', $reverse ? 'DESC' : 'ASC');
    }

    /**
     * Scope a query to sort encounters by newest first.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortNewest($query)
    {
        return $query->orderBy('id', 'DESC');
    }

    /**
     * Scope a query to sort features oldest first.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortOldest($query)
    {
        return $query->orderBy('id');
    }

         /**
     * Scope a query to show only visible features.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query, $withHidden = 0)
    {
        if($withHidden) return $query;
        return $query->where('is_active', 1);
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Displays the model's name, linked to its encyclopedia page.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->url.'" class="display-encounter">'.$this->name.'</a>';
    }

    /**
     * Gets the URL of the model's encyclopedia page.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('encounter-areas/'.$this->id);
    }

    /**
     * Rolls on the loot table and consolidates the rewards.
     *
     * @param  int  $quantity
     * @return \Illuminate\Support\Collection
     */
    public function roll($quantity = 1)
    { 
        $rewards = createAssetsArray();

        $loot = $this->encounters;
        $totalWeight = 0;
        foreach($loot as $l) $totalWeight += $l->weight;

        for($i = 0; $i < $quantity; $i++)
        {
            $roll = mt_rand(0, $totalWeight - 1);
            $result = null;
            $prev = null;
            $count = 0;
            foreach($loot as $l)
            {
                $count += $l->weight;

                if($roll < $count)
                {
                    $result = $l;
                    break;
                }
                $prev = $l;
            }
            if(!$result) $result = $prev;

            if($result) {
                // If this is chained to another loot table, roll on that table
                addAsset($rewards, $result->reward, $result->quantity);
            }
        }
        return $rewards;
    }
    

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute()
    {
        return 'images/data/encounters/areas';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getImageFileNameAttribute()
    {
        return $this->id . '-image.png';
    }

    /**
     * Gets the path to the file directory containing the model's image.
     *
     * @return string
     */
    public function getImagePathAttribute()
    {
        return public_path($this->imageDirectory);
    }
    
    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        if (!$this->has_image) return null;
        return asset($this->imageDirectory . '/' . $this->imageFileName);
    }
}