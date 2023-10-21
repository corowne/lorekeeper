<?php

namespace App\Http\Controllers\Admin\Data;

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
    public function getEncounterAreaIndex()
    {
        $encounters = Encounter::all()->pluck('name','id')->toArray();

        return view('admin.encounters.encounter_areas', [
            'areas' => EncounterArea::orderBy('name', 'DESC')->get(),
            'encounters' => $encounters,
        ]);
    }

    /**
     * Shows the create encounter area page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateEncounterArea()
    {
        $encounters = Encounter::all()->pluck('name','id')->toArray();

        if(!count($encounters)) {
            flash('You can\'t create an area without some encounters to fill it!')->error();
            return redirect()->to('admin/data/encounters/');
        }

        return view('admin.encounters.create_edit_encounter_area', [
            'area' => new EncounterArea,
            'encounters' => Encounter::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    /**
     * Shows the edit encounter area page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditEncounterArea($id)
    {
        $area = EncounterArea::find($id);
        if(!$area) abort(404);
        return view('admin.encounters.create_edit_encounter_area', [
            'area' => $area,
            'encounters' => Encounter::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    /**
     * Creates or edits a encounter area.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  App\Services\EncounterService  $service
     * @param  int|null                    $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditEncounterArea(Request $request, EncounterService $service, $id = null)
    {
        $id ? $request->validate(EncounterArea::$updateRules) : $request->validate(EncounterArea::$createRules);
        $data = $request->only([
            'name', 'description', 'image', 'remove_image','is_active','encounter_id', 'weight', 'start_at', 'end_at', 'encounter_id', 'weight',

        ]);
        if($id && $service->updateEncounterArea(EncounterArea::find($id), $data, Auth::user())) {
            flash('Area updated successfully.')->success();
        }
        else if (!$id && $area = $service->createEncounterArea($data, Auth::user())) {
            flash('Area created successfully.')->success();
            return redirect()->to('admin/data/encounters/areas/edit/'.$area->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Gets the encounter area deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteEncounterArea($id)
    {
        $area = EncounterArea::find($id);
        return view('admin.encounters._delete_encounter_area', [
            'area' => $area,
        ]);
    }

    /**
     * Deletes a encounter area.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  App\Services\EncounterService  $service
     * @param  int                         $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteEncounterArea(Request $request, EncounterService $service, $id)
    {
        if($id && $service->deleteEncounterArea(EncounterArea::find($id))) {
            flash('Area deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/encounters/areas');
    }

    
    /**
     * Gets the loot table test roll modal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\WeatherService  $service
     * @param  int                       $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getRollArea(Request $request, EncounterService $service, $id)
    { 
        $table = EncounterArea::find($id);
        if(!$table) abort(404);

        // Normally we'd merge the result tables, but since we're going to be looking at
        // the results of each roll individually on this page, we'll keep them separate
        $results = [];
        for ($i = 0; $i < $request->get('quantity'); $i++)
            $results[] = $table->roll();

        return view('admin.encounters._roll_area', [
            'table' => $table,
            'results' => $results,
            'quantity' => $request->get('quantity')
        ]);
    }


     /**********************************************************************************************

        ENCOUNTERS

    **********************************************************************************************/



    /**
     * Shows the encounter index.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEncounterIndex()     
    {
        $query = Encounter::query();
        if(isset($data['name']))
            $query->where('name', 'LIKE', '%'.$data['name'].'%');
        return view('admin.encounters.encounters', [
            'encounters' => $query->paginate(20),
        ]);
    }

    /**
     * Shows the create encounter page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateEncounter()
    {
        return view('admin.encounters.create_edit_encounter', [
            'encounter' => new Encounter,
            'items' => Item::orderBy('name')->pluck('name', 'id'),
            'currencies' => Currency::where('is_user_owned', 1)->orderBy('name')->pluck('name', 'id'),
            'tables' => LootTable::orderBy('name')->pluck('name', 'id'),
            'raffles' => Raffle::where('rolled_at', null)->where('is_active', 1)->orderBy('name')->pluck('name', 'id'),
        ]);
    }

    /**
     * Shows the edit encounter page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditEncounter($id)
    {
        $encounter = Encounter::find($id);
        if(!$encounter) abort(404);
        return view('admin.encounters.create_edit_encounter', [
            'encounter' => $encounter,
            'items' => Item::orderBy('name')->pluck('name', 'id'),
            'currencies' => Currency::where('is_user_owned', 1)->orderBy('name')->pluck('name', 'id'),
            'tables' => LootTable::orderBy('name')->pluck('name', 'id'),
            'raffles' => Raffle::where('rolled_at', null)->where('is_active', 1)->orderBy('name')->pluck('name', 'id'),
        ]);
    }

    /**
     * Creates or edits an encounter.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  App\Services\EncounterService  $service
     * @param  int|null                    $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditEncounter(Request $request, EncounterService $service, $id = null)
    {
        $id ? $request->validate(Encounter::$updateRules) : $request->validate(Encounter::$createRules);
        $data = $request->only([
            'name', 'description', 'image', 'remove_image', 'initial_prompt', 'option_name', 'option_description', 'option_reward', 'is_active', 'rewardable_type', 'rewardable_id', 'quantity', 'start_at', 'end_at',
        ]);
        if($id && $service->updateEncounter(Encounter::find($id), $data, Auth::user())) {
            flash('Encounter updated successfully.')->success();
        }
        else if (!$id && $encounter = $service->createEncounter($data, Auth::user())) {
            flash('Encounter created successfully.')->success();
            return redirect()->to('admin/data/encounters/edit/'.$encounter->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

     /**
     * Gets the encounter deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteEncounter($id)
    {
        $encounter = Encounter::find($id);
        return view('admin.encounters._delete_encounter', [
            'encounter' => $encounter,
        ]);
    }

    /**
     * Deletes a encounter.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  App\Services\EncounterService  $service
     * @param  int                         $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteEncounter(Request $request, EncounterService $service, $id)
    {
        if($id && $service->deleteEncounter(Encounter::find($id))) {
            flash('Encounter deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/encounters');
    }
}

