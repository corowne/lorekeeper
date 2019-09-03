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
use App\Models\User\UserCharacterLog;
use App\Models\Submission\Submission;
use App\Models\Submission\SubmissionCharacter;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'alias', 'rank_id', 'email', 'password', 'is_news_unread'
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

    protected $appends = [
        'verified_name'
    ];

    public $timestamps = true;

    public function settings() 
    {
        return $this->hasOne('App\Models\User\UserSettings');
    }

    public function notifications() 
    {
        return $this->hasMany('App\Models\Notification');
    }
    
    public function characters() 
    {
        return $this->hasMany('App\Models\Character\Character')->where('is_myo_slot', 0)->orderBy('sort', 'DESC');
    }
    
    public function myoSlots() 
    {
        return $this->hasMany('App\Models\Character\Character')->where('is_myo_slot', 1)->orderBy('id', 'DESC');
    }
    
    public function rank() 
    {
        return $this->belongsTo('App\Models\Rank\Rank');
    }

    public function items()
    {
        return $this->belongsToMany('App\Models\Item\Item', 'user_items')->withPivot('data', 'updated_at', 'id')->whereNull('user_items.deleted_at')->whereNull('user_items.holding_type');
    }

    public function canEditRank($rank)
    {
        return $this->rank->canEditRank($rank);
    }

    public function getVerifiedNameAttribute()
    {
        return $this->name . ($this->hasAlias ? '' : ' (Unverified)');
    }

    public function getHasAliasAttribute() 
    {
        return !is_null($this->alias);
    }

    public function getIsAdminAttribute()
    {
        return $this->rank->isAdmin;
    }

    public function getIsStaffAttribute()
    {
        return (RankPower::where('rank_id', $this->rank_id)->exists() || $this->isAdmin);
    }

    public function hasPower($power)
    {
        return $this->rank->hasPower($power); 
    }

    public function getPowers()
    {
        return $this->rank->getPowers();
    }

    public function getUrlAttribute()
    {
        return url('user/'.$this->name);
    }

    public function getAdminUrlAttribute()
    {
        return url('admin/users/'.$this->name.'/edit');
    }

    public function getAliasUrlAttribute()
    {
        if(!$this->alias) return null;
        return 'https://www.deviantart.com/'.$this->alias;
    }

    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->url.'" class="display-user" '.($this->rank->color ? 'style="color: #'.$this->rank->color.';"' : '').'>'.$this->name.'</a>';
    }

    public function getDisplayAliasAttribute()
    {
        if (!$this->alias) return '(Unverified)';
        return '<a href="'.$this->aliasUrl.'">'.$this->alias.'@dA</a>';
    }

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

    public function getCurrencySelect()
    {
        return UserCurrency::where('user_id', $this->id)->leftJoin('currencies', 'user_currencies.currency_id', '=', 'currencies.id')->orderBy('currencies.sort_user', 'DESC')->get()->pluck('name_with_quantity', 'currency_id')->toArray();
    }

    public function getLogTypeAttribute()
    {
        return 'User';
    }

    public function getCurrencyLogs($limit = 10)
    {
        $user = $this;
        $query = CurrencyLog::where(function($query) use ($user) {
            $query->where('sender_type', 'User')->where('sender_id', $user->id)->whereNotIn('log_type', ['Staff Grant', 'Prompt Rewards']);
        })->orWhere(function($query) use ($user) {
            $query->where('recipient_type', 'User')->where('recipient_id', $user->id)->where('log_type', '!=', 'Staff Removal');
        })->orderBy('id', 'DESC');
        if($limit) return $query->take($limit)->get();
        else return $query->paginate(30);
    }

    public function getItemLogs($limit = 10)
    {
        $user = $this;
        $query = ItemLog::where(function($query) use ($user) {
            $query->where('sender_id', $user->id)->whereNotIn('log_type', ['Staff Grant', 'Staff Removal']);
        })->orWhere(function($query) use ($user) {
            $query->where('recipient_id', $user->id);
        })->orderBy('id', 'DESC');
        if($limit) return $query->take($limit)->get();
        else return $query->paginate(30);
    }

    public function updateCharacters()
    {
        if(!$this->alias) return;

        // Find any uncredited characters and credit them.
        if(Character::where('owner_alias', $this->alias)->update([
            'owner_alias' => null,
            'user_id' => $this->id
        ])) {
            $count = $this->characters->count();
            if($count) {
                $this->settings->is_fto = 0;
                $this->settings->character_count = $count;
                $this->settings->save();
            }
        }
    }

    public function getOwnershipLogs()
    {
        $user = $this;
        $query = UserCharacterLog::where(function($query) use ($user) {
            $query->where('sender_id', $user->id)->where('log_type', '!=', 'Character Created');
        })->orWhere(function($query) use ($user) {
            $query->where('recipient_id', $user->id);
        })->orderBy('id', 'DESC');
        return $query->paginate(30);
    }

    public function getSubmissions()
    {
        return Submission::where('status', 'Approved')->where('user_id', $this->id)->orderBy('id', 'DESC')->paginate(30);
    }
}
