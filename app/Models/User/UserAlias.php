<?php

namespace App\Models\User;

use Config;
use App\Models\Model;

class UserAlias extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'site', 'alias', 'is_visible', 'is_primary_alias'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_aliases';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the user this set of settings belongs to.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User\User', 'user_id');
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Gets the URL for the user's account on a given site.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        if($this->site == 'tumblr') return 'https://'.$this->alias.Config::get('lorekeeper.sites.tumblr.link');
        else return 'https://'.Config::get('lorekeeper.sites.'.$this->site.'.link').'/'.$this->alias;
    }

    /**
     * Displays the user's alias, linked to the appropriate site.
     *
     * @return string
     */
    public function getDisplayAliasAttribute()
    {
        return '<a href="'.$this->url.'">'.$this->alias.'@'.$this->site.'</a>';
    }
}
