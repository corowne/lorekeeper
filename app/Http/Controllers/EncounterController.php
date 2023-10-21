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
            flash('You don\'t have more energy to explore.')->error();
        return redirect()->back();
        }

        $area = EncounterArea::find($id);
        if (!$area) {
            abort(404);
        }

        //do the rolling here when it works
        //for now we test with id 6

        $encounter = Encounter::find(1);
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

