<?php

namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;

use Auth;

use App\Models\Species;

use App\Services\SpeciesService;

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
            'species' => new Species
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
            'species' => $species
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
            'name', 'description', 'image', 'remove_image'
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
}
