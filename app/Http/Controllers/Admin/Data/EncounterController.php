<?php

namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;

use Auth;

use App\Models\Encounter\Encounter;
use App\Models\Item\Item;
use App\Models\Currency\Currency;
use App\Models\Loot\LootTable;
use App\Models\Raffle\Raffle;

use App\Services\EncounterService;

use App\Http\Controllers\Controller;


class EncounterController extends Controller
{
    /**
     * Shows the prompt category index.
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
     * Shows the create feature category page.
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
            'name', 'initial_prompt', 'proceed_prompt', 'dont_proceed_prompt', 'is_active'
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
}

