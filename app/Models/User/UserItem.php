<?php

namespace App\Models\User;

use App\Models\Model;

class UserItem extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'quantity'
    ];
    //public $primaryKey = 'user_id';
    protected $table = 'user_items';

    public function user() 
    {
        return $this->belongsTo('App\Models\User\User');
    }
    
    public function item() 
    {
        return $this->belongsTo('App\Models\Item\Item');
    }
}
