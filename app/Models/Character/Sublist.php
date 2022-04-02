<?php

namespace App\Models\Character;

use App\Models\Model;

class Sublist extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'key', 'show_main', 'sort',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'masterlist_sub';
    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required|unique:masterlist_sub|between:3,25',
        'key'  => 'required|unique:masterlist_sub|between:3,25|alpha_dash',
    ];

    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => 'required|between:3,25',
        'key'  => 'required|between:3,25|alpha_dash',
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get all character categories associated with the sub list.
     */
    public function categories()
    {
        return $this->hasMany('App\Models\Character\CharacterCategory', 'masterlist_sub_id');
    }

    /**
     * Get all character categories associated with the sub list.
     */
    public function species()
    {
        return $this->hasMany('App\Models\Species\Species', 'masterlist_sub_id');
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Gets the sub masterlist's page's URL.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('sublist/'.$this->key);
    }
}
