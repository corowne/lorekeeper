<?php

namespace App\Models\User;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Models\Character\Character;
use App\Models\Rank\RankPower;
use App\Models\Currency\Currency;
use App\Models\Currency\CurrencyLog;
use App\Models\Item\ItemLog;
use App\Models\Shop\ShopLog;
use App\Models\User\UserCharacterLog;
use App\Models\Submission\Submission;
use App\Models\Submission\SubmissionCharacter;
use App\Models\Character\CharacterBookmark;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'alias', 'rank_id', 'email', 'password', 'is_news_unread', 'is_banned'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Accessors to append to the model.
     *
     * @var array
     */
    protected $appends = [
        'verified_name'
    ];

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/

    /**
     * Get user settings.
     */
    public function settings() 
    {
        return $this->hasOne('App\Models\User\UserSettings');
    }

    /**
     * Get user-editable profile data.
     */
    public function profile() 
    {
        return $this->hasOne('App\Models\User\UserProfile');
    }

    /**
     * Get the user's notifications.
     */
    public function notifications() 
    {
        return $this->hasMany('App\Models\Notification');
    }
    
    /**
     * Get all the user's characters, regardless of whether they are full characters of myo slots.
     */
    public function allCharacters() 
    {
        return $this->hasMany('App\Models\Character\Character')->orderBy('sort', 'DESC');
    }
    
    /**
     * Get the user's characters.
     */
    public function characters() 
    {
        return $this->hasMany('App\Models\Character\Character')->where('is_myo_slot', 0)->orderBy('sort', 'DESC');
    }
    
    /**
     * Get the user's MYO slots.
     */
    public function myoSlots() 
    {
        return $this->hasMany('App\Models\Character\Character')->where('is_myo_slot', 1)->orderBy('id', 'DESC');
    }
    
    /**
     * Get the user's rank data.
     */
    public function rank() 
    {
        return $this->belongsTo('App\Models\Rank\Rank');
    }
    
    /**
     * Get the user's items.
     */
    public function items()
    {
        return $this->belongsToMany('App\Models\Item\Item', 'user_items')->withPivot('data', 'updated_at', 'id')->whereNull('user_items.deleted_at')->whereNull('user_items.holding_type');
    }

    /**********************************************************************************************
    
        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to only include visible (non-banned) users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query)
    {
        return $query->where('is_banned', 0);
    }

    /**********************************************************************************************
    
        ACCESSORS

    **********************************************************************************************/

    /**
     * Get the user's alias.
     *
     * @return string
     */
    public function getVerifiedNameAttribute()
    {
        return $this->name . ($this->hasAlias ? '' : ' (Unverified)');
    }

    /**
     * Checks if the user has an alias (has an associated dA account).
     *
     * @return bool
     */
    public function getHasAliasAttribute() 
    {
        return !is_null($this->alias);
    }

    /**
     * Checks if the user has an admin rank.
     *
     * @return bool
     */
    public function getIsAdminAttribute()
    {
        return $this->rank->isAdmin;
    }

    /**
     * Checks if the user is a staff member with powers.
     *
     * @return bool
     */
    public function getIsStaffAttribute()
    {
        return (RankPower::where('rank_id', $this->rank_id)->exists() || $this->isAdmin);
    }

    /**
     * Checks if the user has the given power.
     *
     * @return bool
     */
    public function hasPower($power)
    {
        return $this->rank->hasPower($power); 
    }

    /**
     * Gets the powers associated with the user's rank.
     *
     * @return array
     */
    public function getPowers()
    {
        return $this->rank->getPowers();
    }

    /**
     * Gets the user's profile URL.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('user/'.$this->name);
    }

    /**
     * Gets the URL for editing the user in the admin panel.
     *
     * @return string
     */
    public function getAdminUrlAttribute()
    {
        return url('admin/users/'.$this->name.'/edit');
    }

    /**
     * Gets the URL for the user's deviantART account.
     *
     * @return string
     */
    public function getAliasUrlAttribute()
    {
        if(!$this->alias) return null;
        return 'https://www.deviantart.com/'.$this->alias;
    }

    /**
     * Displays the user's name, linked to their profile page.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return ($this->is_banned ? '<strike>' : '') . '<a href="'.$this->url.'" class="display-user" '.($this->rank->color ? 'style="color: #'.$this->rank->color.';"' : '').'>'.$this->name.'</a>' . ($this->is_banned ? '</strike>' : '');
    }

    /**
     * Displays the user's alias, linked to their deviantART page.
     *
     * @return string
     */
    public function getDisplayAliasAttribute()
    {
        if (!$this->alias) return '(Unverified)';
        return '<a href="'.$this->aliasUrl.'">'.$this->alias.'@dA</a>';
    }

    /**
     * Gets the user's log type for log creation.
     *
     * @return string
     */
    public function getLogTypeAttribute()
    {
        return 'User';
    }

    /**********************************************************************************************
    
        OTHER FUNCTIONS

    **********************************************************************************************/

    /**
     * Checks if the user can edit the given rank.
     *
     * @return bool
     */
    public function canEditRank($rank)
    {
        return $this->rank->canEditRank($rank);
    }

    /**
     * Get the user's held currencies.
     *
     * @param  bool  $showAll
     * @return \Illuminate\Support\Collection
     */
    public function getCurrencies($showAll = false)
    {
        // Get a list of currencies that need to be displayed
        // On profile: only ones marked is_displayed
        // In bank: ones marked is_displayed + the ones the user has

        $owned = UserCurrency::where('user_id', $this->id)->pluck('quantity', 'currency_id')->toArray();

        $currencies = Currency::where('is_user_owned', 1);
        if($showAll) $currencies->where(function($query) use($owned) {
            $query->where('is_displayed', 1)->orWhereIn('id', array_keys($owned));
        });
        else $currencies = $currencies->where('is_displayed', 1);

        $currencies = $currencies->orderBy('sort_user', 'DESC')->get();

        foreach($currencies as $currency) {
            $currency->quantity = isset($owned[$currency->id]) ? $owned[$currency->id] : 0;
        }

        return $currencies;
    }

    /**
     * Get the user's held currencies as an array for select inputs.
     *
     * @return array
     */
    public function getCurrencySelect($isTransferrable = false)
    {
        $query = UserCurrency::query()->where('user_id', $this->id)->leftJoin('currencies', 'user_currencies.currency_id', '=', 'currencies.id')->orderBy('currencies.sort_user', 'DESC');
        if($isTransferrable) $query->where('currencies.allow_user_to_user', 1);
        return $query->get()->pluck('name_with_quantity', 'currency_id')->toArray();
    }

    /**
     * Get the user's currency logs.
     *
     * @param  int  $limit
     * @return \Illuminate\Support\Collection|\Illuminate\Pagination\LengthAwarePaginator
     */
    public function getCurrencyLogs($limit = 10)
    {
        $user = $this;
        $query = CurrencyLog::with('currency')->where(function($query) use ($user) {
            $query->with('sender')->where('sender_type', 'User')->where('sender_id', $user->id)->whereNotIn('log_type', ['Staff Grant', 'Prompt Rewards', 'Claim Rewards']);
        })->orWhere(function($query) use ($user) {
            $query->with('recipient')->where('recipient_type', 'User')->where('recipient_id', $user->id)->where('log_type', '!=', 'Staff Removal');
        })->orderBy('id', 'DESC');
        if($limit) return $query->take($limit)->get();
        else return $query->paginate(30);
    }

    /**
     * Get the user's item logs.
     *
     * @param  int  $limit
     * @return \Illuminate\Support\Collection|\Illuminate\Pagination\LengthAwarePaginator
     */
    public function getItemLogs($limit = 10)
    {
        $user = $this;
        $query = ItemLog::with('sender')->with('recipient')->with('item')->where(function($query) use ($user) {
            $query->where('sender_id', $user->id)->whereNotIn('log_type', ['Staff Grant', 'Staff Removal']);
        })->orWhere(function($query) use ($user) {
            $query->where('recipient_id', $user->id);
        })->orderBy('id', 'DESC');
        if($limit) return $query->take($limit)->get();
        else return $query->paginate(30);
    }

    /**
     * Get the user's shop purchase logs.
     *
     * @param  int  $limit
     * @return \Illuminate\Support\Collection|\Illuminate\Pagination\LengthAwarePaginator
     */
    public function getShopLogs($limit = 10)
    {
        $user = $this;
        $query = ShopLog::where('user_id', $this->id)->with('character')->with('shop')->with('item')->with('currency')->orderBy('id', 'DESC');
        if($limit) return $query->take($limit)->get();
        else return $query->paginate(30);
    }

    /**
     * Get the user's character ownership logs.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getOwnershipLogs()
    {
        $user = $this;
        $query = UserCharacterLog::with('sender.rank')->with('recipient.rank')->with('character')->where(function($query) use ($user) {
            $query->where('sender_id', $user->id)->whereNotIn('log_type', ['Character Created', 'MYO Slot Created', 'Character Design Updated', 'MYO Design Approved']);
        })->orWhere(function($query) use ($user) {
            $query->where('recipient_id', $user->id);
        })->orderBy('id', 'DESC');
        return $query->paginate(30);
    }

    /**
     * Checks if there are characters credited to the user's alias and updates ownership to their account accordingly.
     */
    public function updateCharacters()
    {
        if(!$this->alias) return;

        // Find any uncredited characters and credit them.
        if(Character::where('owner_alias', $this->alias)->update([
            'owner_alias' => null,
            'user_id' => $this->id
        ])) {
            $count = $this->characters->count();
            if($count || $$myoCount) {
                if($count) {
                    $this->settings->is_fto = 0;
                }
                $this->settings->save();
            }
        }
    }

    /**
     * Get the user's submissions.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getSubmissions()
    {
        return Submission::with('user')->with('prompt')->where('status', 'Approved')->where('user_id', $this->id)->orderBy('id', 'DESC')->paginate(30);
    }

    /**
     * Checks if the user has bookmarked a character.
     * Returns the bookmark if one exists.
     *
     * @return \App\Models\Character\CharacterBookmark
     */
    public function hasBookmarked($character)
    {
        return CharacterBookmark::where('user_id', $this->id)->where('character_id', $character->id)->first();
    }
}
