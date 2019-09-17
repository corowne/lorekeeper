<?php

namespace App\Models\User;

use App\Models\Model;

class UserProfile extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'text', 'parsed_text'
    ];
    public $primaryKey = 'user_id';
    protected $table = 'user_profiles';

    public function user() 
    {
        return $this->belongsTo('App\Models\User\User');
    }
}
