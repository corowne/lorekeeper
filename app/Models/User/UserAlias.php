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

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to only include visible aliases.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', 1);
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
        if($this->site == 'tumblr') return 'https://'.$this->alias.'.'.Config::get('lorekeeper.sites.tumblr.link');
        elseif($this->site == 'discord') return null;
        else return 'https://'.Config::get('lorekeeper.sites.'.$this->site.'.link').'/'.$this->alias;
    }

    /**
     * Displays the user's alias, linked to the appropriate site.
     *
     * @return string
     */
    public function getDisplayAliasAttribute()
    {
        if($this->site == 'discord') return '<span>'.$this->alias.'@'.$this->siteDisplayName.'</span>';
        else return '<a href="'.$this->url.'">'.$this->alias.'@'.$this->siteDisplayName.'</a>';
    }

    /**
     * Retrieves the config data for the site.
     *
     * @return string
     */
    public function getConfigAttribute()
    {
        return Config::get('lorekeeper.sites.' . $this->site);
    }

    /**
     * Retrieves the display name of the alias's site.
     *
     * @return string
     */
    public function getSiteDisplayNameAttribute()
    {
        return Config::get('lorekeeper.sites.' . $this->site . '.display_name');
    }

    /**
     * Checks if this alias can be made a primary alias.
     *
     * @return string
     */
    public function getCanMakePrimaryAttribute()
    {
        return Config::get('lorekeeper.sites.' . $this->site . '.primary_alias');
    }
}
