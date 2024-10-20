<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Character\CharacterCurrency;
use App\Models\Character\CharacterItem;
use App\Models\Currency\Currency;
use App\Models\Encounter\Encounter;
use App\Models\Encounter\EncounterArea;
use App\Models\User\UserCurrency;
use App\Models\User\UserItem;
use App\Services\CurrencyManager;
use App\Services\EncounterService;
use Auth;
use Config;
use Illuminate\Http\Request;

/**use App\Models\User\UserPet;
use App\Models\User\UserCollection;
use App\Models\User\UserRecipe;
use App\Models\User\UserEnchantment;
use App\Models\User\UserWeapon;
use App\Models\User\UserGear;
use App\Models\User\UserAward;**/

class EncounterController extends Controller
{
    /**********************************************************************************************

    ENCOUNTER AREAS

     **********************************************************************************************/

    /**
     * Shows the encounter area index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEncounterAreas()
    {
        $use_energy = Config::get('lorekeeper.encounters.use_energy');
        $use_characters = Config::get('lorekeeper.encounters.use_characters');
        $user = Auth::user();

        //get energy val
        if ($use_characters) {
            $character = $user->settings->encounterCharacter ?? null;
            if ($use_energy && isset($character)) {
                $energy = $character->encounter_energy;
            } elseif (isset($character)) {
                $energy = CharacterCurrency::where('character_id', $character->id)
                    ->where('currency_id', Config::get('lorekeeper.encounters.energy_replacement_id'))
                    ->first()->quantity;
            }
        } else {
            if ($use_energy) {
                $energy = $user->settings->encounter_energy;
            } else {
                $energy = UserCurrency::where('user_id', $user->id)
                    ->where('currency_id', Config::get('lorekeeper.encounters.energy_replacement_id'))
                    ->first()->quantity;
            }
        }

        return view('encounters.index', [
            'user' => $user,
            'areas' => EncounterArea::orderBy('name', 'DESC')->active()->get(),
            'characters' => $user->characters()->pluck('slug', 'id'),
            'use_energy' => $use_energy,
            'use_characters' => $use_characters,
            'energy' => $energy ?? null,
            'character' => $character ?? null,
        ]);
    }

    /**
     * explore an area
     *
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function exploreArea($id, EncounterService $service)
    {
        $user = Auth::user();

        $use_characters = Config::get('lorekeeper.encounters.use_characters');

        $area = EncounterArea::find($id);
        if (!$area) {
            abort(404);
        }

        $result = $area->roll(1);
        $encounter = Encounter::find($result->encounter_id);
        if (!$encounter) {
            abort(404);
        }

        //if ajax passed admin variable and user is staff
        if (isset($_GET['admin']) && $user->isStaff) {
            //do nothing lol
            //skip all the checks to get right to testing
            //oh, we should get the prompts though too
            $selectable = $encounter->prompts;
        } else {
            $selectable = [];
            //if character selection
            if ($use_characters) {
                $character = $user->settings->encounterCharacter;
                if (!$character) {
                    header('HTTP/1.1 500  You need to select a character to enter an area.');
                    header('Content-Type: application/json; charset=UTF-8');
                    die(json_encode(array('message' => 'ERROR', 'code' => 500)));
                }

                //if limits, check CHARACTER has them
                if ($area->limits->count()) {
                    if (!$this->checkLimits($user, true, $area, $character)) {
                        header('HTTP/1.1 500  ' . $character->fullName . ' does not have the limits to enter this area.');
                        header('Content-Type: application/json; charset=UTF-8');
                        die(json_encode(array('message' => 'ERROR', 'code' => 500)));
                    }
                }

                //if prompt limits, check CHARACTER has them
                foreach ($encounter->prompts as $prompt) {
                    if ($prompt->limits->count()) {
                        $selectable[] = $this->checkLimits($user, true, $prompt, $character, true);
                    } elseif (!$prompt->limits->count()) {
                        // prompts that DON'T have a limit
                        $selectable[] = $prompt;
                    }
                }

                if (!$this->checkEnergy($user, true, $area, $character)) {
                    header('HTTP/1.1 500 ' . $character->fullName . ' has no energy or an error has occurred.');
                    header('Content-Type: application/json; charset=UTF-8');
                    die(json_encode(array('message' => 'ERROR', 'code' => 500)));
                }

            } else {
                //users are set instead

                //if limits, check USER has them
                if ($area->limits->count()) {
                    if (!$this->checkLimits($user, false, $area)) {
                        header('HTTP/1.1 500 you do not have the limits to enter this area.');
                        header('Content-Type: application/json; charset=UTF-8');
                        die(json_encode(array('message' => 'ERROR', 'code' => 500)));
                    }

                }

                //if prompt limits, check USER has them
                foreach ($encounter->prompts as $prompt) {
                    if ($prompt->limits->count()) {
                        $selectable[] = $this->checkLimits($user, false, $prompt, null, true);
                    } elseif (!$prompt->limits->count()) {
                        // prompts that DON'T have a limit
                        $selectable[] = $prompt;
                    }
                }

                if (!$this->checkEnergy($user, false, $area)) {
                    header('HTTP/1.1 500 You have no energy or an error has occurred.');
                    header('Content-Type: application/json; charset=UTF-8');
                    die(json_encode(array('message' => 'ERROR', 'code' => 500)));
                }

            }
            $selectable = array_filter($selectable);
        }

        return view('encounters.encounter', [
            'area' => $area,
            'areas' => EncounterArea::orderBy('name', 'DESC')->active()->get(),
            'encounter' => $encounter,
            'action_options' => $selectable,
        ]);
    }

    /**
     * take encounter action.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  App\Services\EncounterService  $service
     * @param  int|null                    $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postAct(Request $request, EncounterService $service, $id)
    {
        $data = $request->only(['action', 'area_id', 'encounter_id']);
        if ($id && $service->takeAction(EncounterArea::find($id), $data, Auth::user())) {
            return redirect()->to('encounter-areas');
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }
        return redirect()->back();
    }

    /**
     * Change selected character.
     *
     */
    public function postSelectCharacter(Request $request, EncounterService $service)
    {
        $id = $request->input('character_id');
        if ($service->selectCharacter(Auth::user(), $id)) {
            flash('Character selected successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    public function checkLimits($user, $use_characters, $object, $character = null, $prompt = null)
    {
        //let's try and compact some of these checks

        //object is area or prompt
        //check what we should return based on $type

        $use_energy = Config::get('lorekeeper.encounters.use_energy');
        $use_characters = Config::get('lorekeeper.encounters.use_characters');

        //compacting into one check
        //be careful when setting limits if you intend to use characters, as by default they can't own, and therefore, cannot enter an object with certain limits (such as recipes)
        if ($use_characters) {
            foreach ($object->limits as $limit) {
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
                if (isset($prompt)) {
                    if (!$check) {
                        return [];
                    } else {
                        return $object;
                    }
                } elseif (!$check) {
                    return false;
                }
            }
        } else {
            foreach ($object->limits as $limit) {
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

                if (isset($prompt)) {
                    if (!$check) {
                        return [];
                    } else {
                        return $object;
                    }
                } elseif (!$check) {
                    return false;
                }
            }

        }
        if (!isset($prompt)) {
            return true;
        }
    }

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
}
