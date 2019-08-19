<?php

namespace App\Models;

use Config;
use DB;
use Carbon\Carbon;
use App\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MyoSlot extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'character_id', 'name', 'description', 'parsed_description',
        'is_used', 'data', 'rarity_id', 'species_id'
    ];
    protected $table = 'myo_slots';
    public $timestamps = true;
    
    public static $createRules = [
        'name' => 'required',

    ];
    
    public static $updateRules = [
        'name' => 'required',
    ];
    
    public function user() 
    {
        return $this->belongsTo('App\Models\User\User', 'user_id');
    }
    
    public function character() 
    {
        return $this->belongsTo('App\Models\Character\Character', 'character_id');
    }
    
    public function rarity() 
    {
        return $this->belongsTo('App\Models\Rarity', 'rarity_id');
    }
    
    public function species() 
    {
        return $this->belongsTo('App\Models\Species', 'species_id');
    }

    public function scopeUnused($query)
    {
        return $query->where('is_used', 0);
    }

    public function scopeSortOldest($query)
    {
        return $query->orderBy('id');
    }
    
    public function getDataAttribute()
    {
        return json_decode($this->attributes['data'], true);
    }

    public function getViewUrlAttribute()
    {
        return url('myos/view/'.$this->id);
    }

    public function getAdminUrlAttribute()
    {
        return url('admin/myos/edit/'.$this->id);
    }
    
    public function getDisplayOwnerAttribute()
    {
        if($this->user_id) return $this->user->displayName;
        else return '<a href="https://www.deviantart.com/'.$this->owner_alias.'">'.$this->owner_alias.'@dA</a>';
    }
}
