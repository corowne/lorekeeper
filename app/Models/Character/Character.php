<?php

namespace App\Models\Character;

use Config;
use DB;
use App\Models\Model;

use App\Models\User\User;
use App\Models\Character\CharacterCategory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Character extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_image_id', 'character_category_id', 'rarity_id', 'user_id', 
        'owner_alias', 'number', 'slug', 'description', 'parsed_description', 
        'is_sellable', 'is_tradeable', 'is_giftable',
        'sale_value', 'transferrable_at', 'is_visible',
        'is_gift_art_allowed', 'is_trading'
    ];
    protected $table = 'characters';
    public $timestamps = true;
    public $dates = ['transferrable_at'];
    
    public static $createRules = [
        'character_category_id' => 'required',
        'rarity_id' => 'required',
        'user_id' => 'nullable',
        'number' => 'required',
        'slug' => 'required|unique:characters,slug',
        'description' => 'nullable',
        'sale_value' => 'nullable',
    ];
    
    public static $updateRules = [
        'character_category_id' => 'required',
        'rarity_id' => 'required',
        'user_id' => 'required',
        'number' => 'required',
        'slug' => 'required',
        'description' => 'nullable',
        'sale_value' => 'required|min:0',
    ];
    
    public function user() 
    {
        return $this->belongsTo('App\Models\User\User', 'user_id');
    }
    
    public function category() 
    {
        return $this->belongsTo('App\Models\Character\CharacterCategory', 'character_category_id');
    }
    
    public function image() 
    {
        return $this->belongsTo('App\Models\Character\CharacterImage', 'character_image_id');
    }
    
    public function images() 
    {
        return $this->hasMany('App\Models\Character\CharacterImage', 'character_id')->guest();
    }

    public function profile() 
    {
        return $this->hasOne('App\Models\Character\CharacterProfile', 'character_id');
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', 1);
    }

    public function scopeTrading($query)
    {
        return $query->where('is_trading', 1);
    }
    
    public function getDisplayOwnerAttribute()
    {
        if($this->user_id) return $this->user->displayName;
        else return '<a href="https://www.deviantart.com/'.$this->owner_alias.'">'.$this->owner_alias.'@dA</a>';
    }

    
    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->url.'" class="display-character">'.$this->fullName.'</a>';
    }

    public function getFullNameAttribute()
    {
        return $this->slug . ($this->name ? ': '.$this->name : '');
    }

    public function getImageDirectoryAttribute()
    {
        return 'images/data/items';
    }

    public function getImageFileNameAttribute()
    {
        return $this->id . '-image.png';
    }

    public function getImagePathAttribute()
    {
        return public_path($this->imageDirectory);
    }
    
    public function getImageUrlAttribute()
    {
        if (!$this->has_image) return null;
        return asset($this->imageDirectory . '/' . $this->imageFileName);
    }

    public function getUrlAttribute()
    {
        return url('character/'.$this->slug);
    }

    public function updateOwner()
    {
        // Return if the character has an owner on the site already.
        if($this->user_id) return;

        // Check if the owner has an account and update the character's user ID for them.
        $owner = User::where('alias', $this->owner_alias)->first();
        if($owner) {
            $this->user_id = $owner->id;
            $this->owner_alias = null;
            $this->save();

            $owner->settings->is_fto = 0;
            $owner->settings->character_count++;
            $owner->settings->save();
        }
    }
}
