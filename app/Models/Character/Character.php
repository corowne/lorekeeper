<?php

namespace App\Models\Character;

use Config;
use DB;
use App\Models\Model;

use App\Models\User\User;
use App\Models\Character\Character;
use App\Models\User\UserCharacterLog;
use App\Models\Character\CharacterCategory;
use App\Models\Character\CharacterCurrency;
use App\Models\Currency\Currency;
use App\Models\Currency\CurrencyLog;
use App\Models\Submission\Submission;
use App\Models\Submission\SubmissionCharacter;
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
        'is_gift_art_allowed', 'is_trading', 'sort',
        'is_myo_slot', 'name'
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
        'image' => 'required|mimes:jpeg,gif,png|max:20000',
        'thumbnail' => 'nullable|mimes:jpeg,gif,png|max:20000',
    ];
    
    public static $updateRules = [
        'character_category_id' => 'required',
        'number' => 'required',
        'slug' => 'required',
        'description' => 'nullable',
        'sale_value' => 'nullable',
    ];
    
    public static $myoRules = [
        'rarity_id' => 'nullable',
        'user_id' => 'nullable',
        'number' => 'nullable',
        'slug' => 'nullable',
        'description' => 'nullable',
        'sale_value' => 'nullable',
        'name' => 'required',
        'image' => 'nullable|mimes:jpeg,gif,png|max:20000',
        'thumbnail' => 'nullable|mimes:jpeg,gif,png|max:20000',
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

    public function designUpdate() 
    {
        return $this->hasMany('App\Models\Character\CharacterDesignUpdate', 'character_id');
    }

    public function scopeMyo($query, $isMyo = false)
    {
        return $query->where('is_myo_slot', $isMyo);
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

    public function getSlugAttribute()
    {
        if($this->is_myo_slot) return $this->name;
        else return $this->attributes['slug'];
    }
    
    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->url.'" class="display-character">'.$this->fullName.'</a>';
    }

    public function getFullNameAttribute()
    {
        if($this->is_myo_slot) return $this->name;
        else return $this->slug . ($this->name ? ': '.$this->name : '');
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
        if($this->is_myo_slot) return url('myo/'.$this->id);
        else return url('character/'.$this->slug);
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
    public function getCurrencies($displayedOnly = false)
    {
        // Get a list of currencies that need to be displayed
        // On profile: only ones marked is_displayed
        // In bank: ones marked is_displayed + the ones the user has

        $owned = CharacterCurrency::where('character_id', $this->id)->pluck('quantity', 'currency_id')->toArray();

        $currencies = Currency::where('is_character_owned', 1);
        if($displayedOnly) $currencies->where(function($query) use($owned) {
            $query->where('is_displayed', 1)->orWhereIn('id', array_keys($owned));
        });
        else $currencies = $currencies->where('is_displayed', 1);

        $currencies = $currencies->orderBy('sort_character', 'DESC')->get();

        foreach($currencies as $currency) {
            $currency->quantity = isset($owned[$currency->id]) ? $owned[$currency->id] : 0;
        }

        return $currencies;
    }

    public function getCurrencySelect()
    {
        return CharacterCurrency::where('character_id', $this->id)->leftJoin('currencies', 'character_currencies.currency_id', '=', 'currencies.id')->orderBy('currencies.sort_character', 'DESC')->get()->pluck('name_with_quantity', 'currency_id')->toArray();
    }

    public function getCurrencyLogs($limit = 10)
    {
        $character = $this;
        $query = CurrencyLog::where(function($query) use ($character) {
            $query->where('sender_type', 'Character')->where('sender_id', $character->id)->where('log_type', '!=', 'Staff Grant');
        })->orWhere(function($query) use ($character) {
            $query->where('recipient_type', 'Character')->where('recipient_id', $character->id)->where('log_type', '!=', 'Staff Removal');
        })->orderBy('id', 'DESC');
        if($limit) return $query->take($limit)->get();
        else return $query->paginate(30);
    }

    public function getOwnershipLogs()
    {
        $query = UserCharacterLog::where('character_id', $this->id)->orderBy('id', 'DESC');
        return $query->paginate(30);
    }

    public function getCharacterLogs()
    {
        $query = CharacterLog::where('character_id', $this->id)->orderBy('id', 'DESC');
        return $query->paginate(30);
    }

    public function getLogTypeAttribute()
    {
        return 'Character';
    }

    public function getSubmissions()
    {
        return Submission::where('status', 'Approved')->whereIn('id', SubmissionCharacter::where('character_id', $this->id)->pluck('submission_id')->toArray())->paginate(30);

        // Untested
        //$character = $this;
        //return Submission::where('status', 'Approved')->with(['characters' => function($query) use ($character) {
        //    $query->where('submission_characters.character_id', 1);
        //}])  
        //->whereHas('characters', function($query) use ($character) {
        //    $query->where('submission_characters.character_id', 1);
        //});  
        //return Submission::where('status', 'Approved')->where('user_id', $this->id)->orderBy('id', 'DESC')->paginate(30);
    }
}
