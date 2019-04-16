<?php

namespace App\Models;

use App\Models\Model;

class Invitation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'user_id', 'recipient_id'
    ];
    protected $table = 'invitations';
    public $timestamps = true;

    public function user() 
    {
        return $this->belongsTo('App\Models\User\User');
    }

    public function recipient() 
    {
        return $this->belongsTo('App\Models\User\User', 'recipient_id');
    }
}
