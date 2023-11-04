<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use Config;
use DB;

use App\Models\Encounter\Encounter;
use App\Models\Encounter\EncounterArea;
use App\Models\Item\Item;
use App\Models\Currency\Currency;
use App\Models\Loot\LootTable;
use App\Models\Raffle\Raffle;

use App\Services\EncounterService;

use App\Http\Controllers\Controller;

use App\Models\User\UserItem;
use App\Models\User\UserCurrency;

use App\Models\Character\CharacterItem;
use App\Models\Character\CharacterCurrency;
use App\Services\CurrencyManager;

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

        return view('encounters.index', [
            'user' => Auth::user(),
            'areas' => EncounterArea::orderBy('name', 'DESC')
                ->active()
                ->get(),
            'characters' => Auth::user()
                ->characters()
                ->pluck('slug', 'id'),
            'use_energy' => $use_energy,
            'use_characters' => $use_characters,
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

        $use_energy = Config::get('lorekeeper.encounters.use_energy');
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

        //if character selection
        if ($use_characters) {
            $character = $user->settings->encounterCharacter;
            if (!$character) {
                flash('You need to select a character to enter an area.')->error();
                return redirect()->back();
            }

            //if limits, check CHARACTER has them
            if ($area->limits->count()) {
                /**check for area limits
                something i tweaked from shop features to be a bit more broad (ty newt <3), pulled this directly from my own site
                so for convenience i'm gonna leave some of the options commented out if anyone also wants to use them
                feel free to delete at your leisure if you dont plan on using those exts
                make sure to un-comment the "use App\Models\User\UserItem;" - like bits at the top if you need to use an ext
                and obviously don't uncomment things that you don't need or have installed, it will error
                you will still need to edit the area page itself to add the js and such**/
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
                                ->where('count', '>', 0)
                                ->first();
                            break;
                    }

                    if (!$check) {
                        flash($character->fullName . ' requires ' . $limit->item->name . ' to enter this area.')->error();
                        return redirect()->back();
                    }
                }
            }

            //if set to use energy
            if ($use_energy) {
                if ($character->encounter_energy < 1) {
                    flash($character->fullName . ' doesn\'t have enough energy to visit an area.')->error();
                    return redirect()->back();
                }

                $character->encounter_energy -= 1;
                $character->save();
            } else {
                //if set to currency instead
                $energy_currency = CharacterCurrency::where('character_id', $character->id)
                    ->where('currency_id', Config::get('lorekeeper.encounters.energy_replacement_id'))
                    ->first();
                if ($energy_currency->quantity < 1) {
                    flash($character->fullName . ' doesn\'t have enough energy to visit an area.')->error();
                    return redirect()->back();
                }

                //debit cost
                if (!(new CurrencyManager())->debitCurrency($character, null, 'Encounter Removal', 'Used to enter ' . $area->name, Currency::find(Config::get('lorekeeper.encounters.energy_replacement_id')), 1)) {
                    flash('Could not debit currency.')->error();
                    return redirect()->back();
                }
            }
        } else {
            //users are set instead

            //if limits, check USER has them
            if ($area->limits->count()) {
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
                                ->where('count', '>', 0)
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
                        flash('You require a ' . $limit->item->name . ' to enter this area.')->error();
                        return redirect()->back();
                    }
                }
            }

            //if set to use energy
            if ($use_energy) {
                if ($user->settings->encounter_energy < 1) {
                    flash('You don\'t have more energy to visit an area.')->error();
                    return redirect()->back();
                }

                //debit energy
                $user->settings->encounter_energy -= 1;
                $user->settings->save();
            } else {
                //if set to currency instead
                $energy_currency = UserCurrency::where('user_id', $user->id)
                    ->where('currency_id', Config::get('lorekeeper.encounters.energy_replacement_id'))
                    ->first();
                if ($energy_currency->quantity < 1) {
                    flash('You don\'t have enough energy to visit an area.')->error();
                    return redirect()->back();
                }

                //debit cost
                if (!(new CurrencyManager())->debitCurrency($user, null, 'Encounter Removal', 'Used to enter ' . $area->name, Currency::find(Config::get('lorekeeper.encounters.energy_replacement_id')), 1)) {
                    flash('Could not debit currency.')->error();
                    return redirect()->back();
                }
            }
        }

        return view('encounters.encounter', [
            'area' => $area,
            'areas' => EncounterArea::orderBy('name', 'DESC')
                ->active()
                ->get(),
            'encounter' => $encounter,
            'action_options' => $encounter->prompts->pluck('name', 'id'),
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
}
