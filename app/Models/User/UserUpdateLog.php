<?php

namespace App\Models\User;

use App\Models\Model;

class UserUpdateLog extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'staff_id', 'user_id', 'data', 'type'
    ];
    public $primaryKey = 'user_id';
    public $timestamps = true;
    protected $table = 'user_update_log';

    public function staff() 
    {
        return $this->belongsTo('App\Models\User\User', 'staff_id');
    }

    public function user() 
    {
        return $this->belongsTo('App\Models\User\User', 'user_id');
    }

    public function getDataAttribute()
    {
        return json_decode($this->attributes['data'], true);
    }
}
