<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;

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

        return view('encounters.index', [
            'user' => Auth::user(),
            'areas' => EncounterArea::orderBy('name', 'DESC')->active()->get(),
        ]);
    }
    
    /**
     * explore an area
     *
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function exploreArea($id, EncounterService $service) {
        $user = Auth::user();
                
        if ($user->settings->encounter_energy < 1) { 
            flash('You don\'t have more energy to visit an area.')->error();
        return redirect()->back();
        }

        $area = EncounterArea::find($id);
        if (!$area) {
            abort(404);
        }

        /**check for area limits
        something i tweaked from shop features to be a bit more broad (ty newt <3), pulled this directly from my own site
        so for convenience i'm gonna leave some of the options commented out if anyone also wants to use them
        feel free to delete at your leisure if you dont plan on using those exts
        make sure to un-comment the "use App\Models\User\UserItem;" - like bits at the top if you need to use an ext
        and obviously don't uncomment things that you don't need or have installed, it will error
        you will still need to edit the area page itself to add the js and such**/
        if($area->limits->count()) {
            foreach($area->limits as $limit)
            {
                $limitType = $limit->item_type;
                $check = NULL;
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

        $result = $area->roll(1);
        $encounter = Encounter::find($result->encounter_id);
        if (!$encounter) {
            abort(404);
        }

        $user->settings->encounter_energy -= 1;
        $user->settings->save();

        return view('encounters.encounter', [
            'area'   => $area,
            'areas' => EncounterArea::orderBy('name', 'DESC')->active()->get(),
            'encounter'   => $encounter,
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
        $data = $request->only([
            'action','area_id','encounter_id'
        ]);
        if ($id && $service->takeAction(EncounterArea::find($id), $data, Auth::user())) {
            return redirect()->to('encounter-areas');
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

}

