<?php

namespace App\Models\User;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'alias', 'rank_id', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public $timestamps = true;

    public function settings() 
    {
        return $this->hasOne('App\Models\User\UserSettings');
    }
    
    public function rank() 
    {
        return $this->belongsTo('App\Models\Rank\Rank');
    }

    public function canEditRank($rank)
    {
        return $this->rank->canEditRank($rank);
    }

    public function getHasAliasAttribute() 
    {
        return !is_null($this->alias);
    }

    public function getIsAdminAttribute()
    {
        return $this->rank->isAdmin;
    }

    public function hasPower($power)
    {
        return $this->rank->hasPower($power); 
    }

    public function getPowers()
    {
        return $this->rank->getPowers();
    }

    public function getUrlAttribute()
    {
        return url('user/'.$this->name);
    }

    public function getAdminUrlAttribute()
    {
        return url('admin/users/edit/'.$this->name);
    }

    public function getAliasUrlAttribute()
    {
        if(!$this->alias) return null;
        return 'https://www.deviantart.com/'.$this->alias;
    }

    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->url.'" class="display-user" '.($this->rank->color ? 'style="color: #'.$this->rank->color.';"' : '').'>'.$this->name.'</a>';
    }

    public function getDisplayAliasAttribute()
    {
        if (!$this->alias) return '(Unverified)';
        return '<a href="'.$this->aliasUrl.'">'.$this->alias.'@dA</a>';
    }
}
