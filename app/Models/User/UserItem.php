<?php

namespace App\Models\User;

use App\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserItem extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'data', 'item_id', 'user_id'
    ];
    public $timestamps = true;
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

    public function getDataAttribute() 
    {
        return json_decode($this->attributes['data'], true);
    }

    public function getIsTransferrableAttribute()
    {
        if(!isset($this->data['disallow_transfer']) && $this->item->allow_transfer) return true;
        return false;
    }
}
