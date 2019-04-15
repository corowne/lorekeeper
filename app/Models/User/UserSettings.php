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
        'is_fto', 'character_count', 'myo_slot_count',
    ];
    public $primaryKey = 'user_id';
    protected $table = 'user_settings';

    public function user() 
    {
        return $this->belongsTo('App\Models\User\User');
    }
}
