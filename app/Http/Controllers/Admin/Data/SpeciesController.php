<?php

namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;

use Auth;

use App\Models\Species\Species;
use App\Models\Species\Subtype;
use App\Models\Character\CharacterDropData;
use App\Models\Item\Item;
use App\Models\Character\Sublist;

use App\Services\SpeciesService;
use App\Services\CharacterDropService;

use App\Http\Controllers\Controller;

class SpeciesController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin / Species Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of character species.
    |
    */

    /**
     * Shows the species index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('admin.specieses.specieses', [
            'specieses' => Species::orderBy('sort', 'DESC')->get()
        ]);
    }
    
    /**
     * Shows the create species page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateSpecies()
    {
        return view('admin.specieses.create_edit_species', [
            'species' => new Species,
            'sublists' => [0 => 'No Sublist'] + Sublist::orderBy('name', 'DESC')->pluck('name', 'id')->toArray()
        ]);
    }
    
    /**
     * Shows the edit species page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditSpecies($id)
    {
        $species = Species::find($id);
        if(!$species) abort(404);
        return view('admin.specieses.create_edit_species', [
            'species' => $species,
            'sublists' => [0 => 'No Sublist'] + Sublist::orderBy('name', 'DESC')->pluck('name', 'id')->toArray()
        ]);
    }

    /**
     * Creates or edits a species.
     *
     * @param  \Illuminate\Http\Request     $request
     * @param  App\Services\SpeciesService  $service
     * @param  int|null                     $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditSpecies(Request $request, SpeciesService $service, $id = null)
    {
        $id ? $request->validate(Species::$updateRules) : $request->validate(Species::$createRules);
        $data = $request->only([
            'name', 'description', 'image', 'remove_image', 'masterlist_sub_id'
        ]);
        if($id && $service->updateSpecies(Species::find($id), $data, Auth::user())) {
            flash('Species updated successfully.')->success();
        }
        else if (!$id && $species = $service->createSpecies($data, Auth::user())) {
            flash('Species created successfully.')->success();
            return redirect()->to('admin/data/species/edit/'.$species->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /**
     * Gets the species deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteSpecies($id)
    {
        $species = Species::find($id);
        return view('admin.specieses._delete_species', [
            'species' => $species,
        ]);
    }

    /**
     * Deletes a species.
     *
     * @param  \Illuminate\Http\Request     $request
     * @param  App\Services\SpeciesService  $service
     * @param  int                          $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteSpecies(Request $request, SpeciesService $service, $id)
    {
        if($id && $service->deleteSpecies(Species::find($id))) {
            flash('Species deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/species');
    }

    /**
     * Sorts species.
     *
     * @param  \Illuminate\Http\Request     $request
     * @param  App\Services\SpeciesService  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSortSpecies(Request $request, SpeciesService $service)
    {
        if($service->sortSpecies($request->get('sort'))) {
            flash('Species order updated successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Shows the subtype index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSubtypeIndex()
    {
        return view('admin.specieses.subtypes', [
            'subtypes' => Subtype::orderBy('sort', 'DESC')->get()
        ]);
    }
    
    /**
     * Shows the create subtype page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateSubtype()
    {
        return view('admin.specieses.create_edit_subtype', [
            'subtype' => new Subtype,
            'specieses' => Species::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray()
        ]);
    }
    
    /**
     * Shows the edit subtype page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditSubtype($id)
    {
        $subtype = Subtype::find($id);
        if(!$subtype) abort(404);
        return view('admin.specieses.create_edit_subtype', [
            'subtype' => $subtype,
            'specieses' => Species::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray()
        ]);
    }

    /**
     * Creates or edits a subtype.
     *
     * @param  \Illuminate\Http\Request     $request
     * @param  App\Services\SpeciesService  $service
     * @param  int|null                     $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditSubtype(Request $request, SpeciesService $service, $id = null)
    {
        $id ? $request->validate(Subtype::$updateRules) : $request->validate(Subtype::$createRules);
        $data = $request->only([
            'species_id', 'name', 'description', 'image', 'remove_image'
        ]);
        if($id && $service->updateSubtype(Subtype::find($id), $data, Auth::user())) {
            flash('Subtype updated successfully.')->success();
        }
        else if (!$id && $subtype = $service->createSubtype($data, Auth::user())) {
            flash('Subtype created successfully.')->success();
            return redirect()->to('admin/data/subtypes/edit/'.$subtype->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /**
     * Gets the subtype deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteSubtype($id)
    {
        $subtype = Subtype::find($id);
        return view('admin.specieses._delete_subtype', [
            'subtype' => $subtype,
        ]);
    }

    /**
     * Deletes a subtype.
     *
     * @param  \Illuminate\Http\Request     $request
     * @param  App\Services\SpeciesService  $service
     * @param  int                          $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteSubtype(Request $request, SpeciesService $service, $id)
    {
        if($id && $service->deleteSubtype(Subtype::find($id))) {
            flash('Subtype deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/subtypes');
    }

    /**
     * Sorts subtypes.
     *
     * @param  \Illuminate\Http\Request     $request
     * @param  App\Services\SpeciesService  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSortSubtypes(Request $request, SpeciesService $service)
    {
        if($service->sortSubtypes($request->get('sort'))) {
            flash('Subtype order updated successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Shows the character drop index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDropIndex()
    {
        return view('admin.specieses.character_drops', [
            'drops' => CharacterDropData::orderBy('species_id', 'ASC')->paginate(20)
        ]);
    }
    
    /**
     * Shows the create character drop data page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateDrop()
    {
        return view('admin.specieses.create_edit_drop', [
            'drop' => new CharacterDropData,
            'specieses' => Species::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'subtypes' => Subtype::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }
    
    /**
     * Shows the edit character drop data page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditDrop($id)
    {
        $characterDrop = CharacterDropData::find($id);
        if(!$characterDrop) abort(404);
        return view('admin.specieses.create_edit_drop', [
            'drop' => $characterDrop,
            'specieses' => Species::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'subtypes' => Subtype::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'items' => Item::orderBy('name')->pluck('name', 'id')
        ]);
    }

    /**
     * Creates or edits character drop data.
     *
     * @param  \Illuminate\Http\Request           $request
     * @param  App\Services\CharacterDropService  $service
     * @param  int|null                           $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditDrop(Request $request, CharacterDropService $service, $id = null)
    {
        $id ? $request->validate(CharacterDropData::$updateRules) : $request->validate(CharacterDropData::$createRules);
        $data = $request->only([
            'species_id', 'label', 'weight', 'drop_frequency', 'drop_interval', 'is_active', 'item_id', 'min_quantity', 'max_quantity'
        ]);
        if($id && $service->updateCharacterDrop(CharacterDropData::find($id), $data, Auth::user())) {
            flash('Character drop updated successfully.')->success();
        }
        else if (!$id && $drop = $service->createCharacterDrop($data, Auth::user())) {
            flash('Character drop created successfully.')->success();
            return redirect()->to('admin/data/character-drops/edit/'.$drop->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /**
     * Gets the character drop data deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteDrop($id)
    {
        $drop = CharacterDropData::find($id);
        return view('admin.specieses._delete_drop', [
            'drop' => $drop,
        ]);
    }

    /**
     * Deletes a subtype.
     *
     * @param  \Illuminate\Http\Request     $request
     * @param  App\Services\SpeciesService  $service
     * @param  int                          $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteDrop(Request $request, SpeciesService $service, $id)
    {
        if($id && $service->deleteDropData(CharacterDropData::find($id))) {
            flash('Drop data deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/character-drops');
    }
}
