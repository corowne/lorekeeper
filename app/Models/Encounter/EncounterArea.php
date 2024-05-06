<?php

namespace App\Models\Encounter;

use App\Models\Character\CharacterCurrency;
use App\Models\Character\CharacterItem;
use App\Models\Currency\Currency;
use App\Models\Encounter\Encounter;
use App\Models\Encounter\EncounterArea;
use App\Models\Model;
use App\Models\User\UserCurrency;
use App\Models\User\UserItem;
use App\Services\CurrencyManager;
use Carbon\Carbon;
use Config;

class EncounterArea extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'parsed_description', 'is_active', 'has_image', 'start_at', 'end_at', 'has_thumbnail'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'encounter_areas';

    /**
     * Dates on the model to convert to Carbon instances.
     *
     * @var array
     */
    public $dates = ['start_at', 'end_at'];

    /**
     * Validation rules for character creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required|between:3,100',
    ];

    /**
     * Validation rules for character updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => 'required|between:3,100',
    ];

    /**********************************************************************************************

    RELATIONS

     **********************************************************************************************/

    /**
     * Get the loot data for this loot table.
     */
    public function encounters()
    {
        return $this->hasMany('App\Models\Encounter\AreaEncounters', 'encounter_area_id');
    }

    /**
     * Get the required items / assets to enter the shop.
     */
    public function limits()
    {
        return $this->hasMany('App\Models\Encounter\AreaLimit');
    }

    /**********************************************************************************************

    SCOPES

     **********************************************************************************************/

    /**
     * Scope a query to sort encounters in alphabetical order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  bool                                   $reverse
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortAlphabetical($query, $reverse = false)
    {
        return $query->orderBy('name', $reverse ? 'DESC' : 'ASC');
    }

    /**
     * Scope a query to sort encounters by newest first.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortNewest($query)
    {
        return $query->orderBy('id', 'DESC');
    }

    /**
     * Scope a query to sort features oldest first.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortOldest($query)
    {
        return $query->orderBy('id');
    }

    /**
     * Scope a query to show only visible features.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query, $withHidden = 0)
    {
        if ($withHidden) {
            return $query;
        }
        return $query->where('is_active', 1);
    }

    /**********************************************************************************************

    ACCESSORS

     **********************************************************************************************/

    /**
     * Displays the model's name, linked to its encyclopedia page.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return '<a href="' . $this->url . '" class="display-encounter">' . $this->name . '</a>';
    }

    /**
     * Gets the URL of the model's encyclopedia page.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('encounter-areas/' . $this->id);
    }

    /**
     * Selects which encounter the user will get in this area.
     *
     *
     * @return object $result
     */
    public function roll($quantity = 1)
    {
        $encounters = $this->encounters;
        $totalWeight = 0;
        foreach ($encounters as $encounter) {
            $totalWeight += $encounter->weight;
        }

        for ($i = 0; $i < $quantity; $i++) {
            $roll = mt_rand(0, $totalWeight - 1);
            $result = null;
            $prev = null;
            $count = 0;
            foreach ($encounters as $l) {
                $count += $encounter->weight;

                if ($roll < $count) {
                    $result = $l;
                    break;
                }
                $prev = $l;
            }
            if (!$result) {
                $result = $prev;
            }
        }

        return $result;
    }

    /**********************************************************************************************

    BACKGROUND IMAGE

     **********************************************************************************************/

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute()
    {
        return 'images/data/encounters/areas';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getImageFileNameAttribute()
    {
        return $this->id . '-image.png';
    }

    /**
     * Gets the path to the file directory containing the model's image.
     *
     * @return string
     */
    public function getImagePathAttribute()
    {
        return public_path($this->imageDirectory);
    }

    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        if (!$this->has_image) {
            return null;
        }
        return asset($this->imageDirectory . '/' . $this->imageFileName);
    }

    /**********************************************************************************************

    THUMBNAIL IMAGE

     **********************************************************************************************/

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getThumbImageDirectoryAttribute()
    {
        return 'images/data/encounters/areas';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getThumbImageFileNameAttribute()
    {
        return $this->id . '-th-image.png';
    }

    /**
     * Gets the path to the file directory containing the model's image.
     *
     * @return string
     */
    public function getThumbImagePathAttribute()
    {
        return public_path($this->imageDirectory);
    }

    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getThumbImageUrlAttribute()
    {
        if (!$this->has_thumbnail) {
            return null;
        }
        return asset($this->thumbImageDirectory . '/' . $this->thumbImageFileName);
    }

    /**********************************************************************************************

    CHECKS

     **********************************************************************************************/

    public function checkEnergy($user, $use_characters, $area, $character = null)
    {
        //let's try and compact some of these checks

        $use_energy = Config::get('lorekeeper.encounters.use_energy');
        $use_characters = Config::get('lorekeeper.encounters.use_characters');

        //if set to use energy
        if ($use_energy) {
            if ($use_characters) {
                if ($character->encounter_energy < 1) {
                    return false;
                }

                $character->encounter_energy -= 1;
                $character->save();
            } else {
                if ($user->settings->encounter_energy < 1) {
                    return false;
                }

                //debit energy
                $user->settings->encounter_energy -= 1;
                $user->settings->save();
            }
        } else {
            if ($use_characters) {
                //if set to currency instead
                $energy_currency = CharacterCurrency::where('character_id', $character->id)
                    ->where('currency_id', Config::get('lorekeeper.encounters.energy_replacement_id'))
                    ->first();
                if ($energy_currency->quantity < 1) {
                    return false;
                }

                //debit cost
                if (!(new CurrencyManager())->debitCurrency($character, null, 'Encounter Removal', 'Used to enter ' . $area->name, Currency::find(Config::get('lorekeeper.encounters.energy_replacement_id')), 1)) {
                    return false;
                }
            } else {
                //if set to currency instead
                $energy_currency = UserCurrency::where('user_id', $user->id)
                    ->where('currency_id', Config::get('lorekeeper.encounters.energy_replacement_id'))
                    ->first();
                if ($energy_currency->quantity < 1) {
                    return false;
                }

                //debit cost
                if (!(new CurrencyManager())->debitCurrency($user, null, 'Encounter Removal', 'Used to enter ' . $area->name, Currency::find(Config::get('lorekeeper.encounters.energy_replacement_id')), 1)) {
                    return false;
                }
            }
        }
        return true;
    }

    public function checkLimits($user, $use_characters, $area, $character = null)
    {
        //let's try and compact some of these checks

        $use_energy = Config::get('lorekeeper.encounters.use_energy');
        $use_characters = Config::get('lorekeeper.encounters.use_characters');

        //compacting into one check
        //be careful when setting limits if you intend to use characters, as by default they can't own, and therefore, cannot enter an area with certain limits (such as recipes)
        if ($use_characters) {
            foreach ($area->limits as $limit) {
                $limitType = $limit->item_type;
                $check = null;
                switch ($limitType) {
                    case 'Item':
                        $check = CharacterItem::where('item_id', $limit->item_id)
                            ->where('character_id', $character->id)
                            ->where('count', '>', 0)
                            ->first();
                        break;
                    case 'Currency':
                        $check = CharacterCurrency::where('currency_id', $limit->item_id)
                            ->where('character_id', $character->id)
                            ->where('quantity', '>', 0)
                            ->first();
                        break;
                }

                if (!$check) {
                    return false;
                }
            }
        } else {
            foreach ($area->limits as $limit) {
                $limitType = $limit->item_type;
                $check = null;
                switch ($limitType) {
                    case 'Item':
                        $check = UserItem::where('item_id', $limit->item_id)
                            ->where('user_id', $user->id)
                            ->where('count', '>', 0)
                            ->first();
                        break;
                    case 'Currency':
                        $check = UserCurrency::where('currency_id', $limit->item_id)
                            ->where('user_id', $user->id)
                            ->where('quantity', '>', 0)
                            ->first();
                        break;
                        /**case 'Recipe':
                $check = UserRecipe::where('recipe_id', $limit->item_id)
                ->where('user_id', $user->id)
                ->first();
                break;
                case 'Collection':
                $check = UserCollection::where('collection_id', $limit->item_id)
                ->where('user_id', $user->id)
                ->first();
                break;
                case 'Enchantment':
                $check = UserEnchantment::where('enchantment_id', $limit->item_id)
                ->whereNull('deleted_at')
                ->where('user_id', $user->id)
                ->first();
                break;
                case 'Weapon':
                $check = UserWeapon::where('weapon_id', $limit->item_id)
                ->whereNull('deleted_at')
                ->where('user_id', $user->id)
                ->first();
                break;
                case 'Gear':
                $check = UserGear::where('gear_id', $limit->item_id)
                ->whereNull('deleted_at')
                ->where('user_id', $user->id)
                ->first();
                break;
                case 'Award':
                $check = UserAward::where('award_id', $limit->item_id)
                ->whereNull('deleted_at')
                ->where('user_id', $user->id)
                ->where('count', '>', 0)
                ->first();
                break;
                case 'Pet':
                $check = UserPet::where('pet_id', $limit->item_id)
                ->whereNull('deleted_at')
                ->where('user_id', $user->id)
                ->where('count', '>', 0)
                ->first();
                break;**/
                }

                if (!$check) {
                    return false;
                }
            }

        }
        return true;
    }
}
