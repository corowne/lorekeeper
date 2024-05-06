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
        } else {
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
                    if (!$area->checkLimits($user, true, $area, $character)) {
                        header('HTTP/1.1 500  ' . $character->fullName . ' does not have the limits to enter this area.');
                        header('Content-Type: application/json; charset=UTF-8');
                        die(json_encode(array('message' => 'ERROR', 'code' => 500)));
                    }

                }

                if (!$area->checkEnergy($user, true, $area, $character)) {
                    header('HTTP/1.1 500 ' . $character->fullName . ' has no energy or an error has occurred.');
                    header('Content-Type: application/json; charset=UTF-8');
                    die(json_encode(array('message' => 'ERROR', 'code' => 500)));
                }
            } else {
                //users are set instead

                //if limits, check USER has them
                if ($area->limits->count()) {
                    if (!$area->checkLimits($user, false, $area)) {
                        header('HTTP/1.1 500 you do not have the limits to enter this area.');
                        header('Content-Type: application/json; charset=UTF-8');
                        die(json_encode(array('message' => 'ERROR', 'code' => 500)));
                    }

                }

                if (!$area->checkEnergy($user, false, $area)) {
                    header('HTTP/1.1 500 You have no energy or an error has occurred.');
                    header('Content-Type: application/json; charset=UTF-8');
                    die(json_encode(array('message' => 'ERROR', 'code' => 500)));
                }

            }
        }

        return view('encounters.encounter', [
            'area' => $area,
            'areas' => EncounterArea::orderBy('name', 'DESC')->active()->get(),
            'encounter' => $encounter,
            'action_options' => $encounter->prompts,
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
