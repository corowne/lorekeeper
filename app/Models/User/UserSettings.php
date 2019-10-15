<?php

namespace App\Models\User;

use App\Models\Model;

class UserSettings extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_fto', 'character_count', 'myo_slot_count', 'submission_count', 'banned_at', 'ban_reason'
    ];
    public $primaryKey = 'user_id';
    protected $table = 'user_settings';
    protected $dates = ['banned_at'];

    public function user() 
    {
        return $this->belongsTo('App\Models\User\User');
    }
}
