<?php

namespace App\Http\Controllers\Admin\Data;

use App\Http\Controllers\Controller;
use App\Models\Character\Sublist;
use App\Models\Species\Species;
use App\Models\Species\Subtype;
use App\Services\SpeciesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpeciesController extends Controller {
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
    public function getIndex() {
        return view('admin.specieses.specieses', [
            'specieses' => Species::orderBy('sort', 'DESC')->get(),
        ]);
    }

    /**
     * Shows the create species page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateSpecies() {
        return view('admin.specieses.create_edit_species', [
            'species'  => new Species,
            'sublists' => [0 => 'No Sublist'] + Sublist::orderBy('name', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Shows the edit species page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditSpecies($id) {
        $species = Species::find($id);
        if (!$species) {
            abort(404);
        }

        return view('admin.specieses.create_edit_species', [
            'species'  => $species,
            'sublists' => [0 => 'No Sublist'] + Sublist::orderBy('name', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Creates or edits a species.
     *
     * @param App\Services\SpeciesService $service
     * @param int|null                    $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditSpecies(Request $request, SpeciesService $service, $id = null) {
        $id ? $request->validate(Species::$updateRules) : $request->validate(Species::$createRules);
        $data = $request->only([
            'name', 'description', 'image', 'remove_image', 'masterlist_sub_id', 'is_visible',
        ]);
        if ($id && $service->updateSpecies(Species::find($id), $data, Auth::user())) {
            flash('Species updated successfully.')->success();
        } elseif (!$id && $species = $service->createSpecies($data, Auth::user())) {
            flash('Species created successfully.')->success();

            return redirect()->to('admin/data/species/edit/'.$species->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the species deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteSpecies($id) {
        $species = Species::find($id);

        return view('admin.specieses._delete_species', [
            'species' => $species,
        ]);
    }

    /**
     * Deletes a species.
     *
     * @param App\Services\SpeciesService $service
     * @param int                         $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteSpecies(Request $request, SpeciesService $service, $id) {
        if ($id && $service->deleteSpecies(Species::find($id))) {
            flash('Species deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/data/species');
    }

    /**
     * Sorts species.
     *
     * @param App\Services\SpeciesService $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSortSpecies(Request $request, SpeciesService $service) {
        if ($service->sortSpecies($request->get('sort'))) {
            flash('Species order updated successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Shows the subtype index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSubtypeIndex() {
        return view('admin.specieses.subtypes', [
            'subtypes' => Subtype::orderBy('sort', 'DESC')->get(),
        ]);
    }

    /**
     * Shows the create subtype page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateSubtype() {
        return view('admin.specieses.create_edit_subtype', [
            'subtype'   => new Subtype,
            'specieses' => Species::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Shows the edit subtype page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditSubtype($id) {
        $subtype = Subtype::find($id);
        if (!$subtype) {
            abort(404);
        }

        return view('admin.specieses.create_edit_subtype', [
            'subtype'   => $subtype,
            'specieses' => Species::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Creates or edits a subtype.
     *
     * @param App\Services\SpeciesService $service
     * @param int|null                    $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditSubtype(Request $request, SpeciesService $service, $id = null) {
        $id ? $request->validate(Subtype::$updateRules) : $request->validate(Subtype::$createRules);
        $data = $request->only([
            'species_id', 'name', 'description', 'image', 'remove_image', 'is_visible',
        ]);
        if ($id && $service->updateSubtype(Subtype::find($id), $data, Auth::user())) {
            flash('Subtype updated successfully.')->success();
        } elseif (!$id && $subtype = $service->createSubtype($data, Auth::user())) {
            flash('Subtype created successfully.')->success();

            return redirect()->to('admin/data/subtypes/edit/'.$subtype->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the subtype deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteSubtype($id) {
        $subtype = Subtype::find($id);

        return view('admin.specieses._delete_subtype', [
            'subtype' => $subtype,
        ]);
    }

    /**
     * Deletes a subtype.
     *
     * @param App\Services\SpeciesService $service
     * @param int                         $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteSubtype(Request $request, SpeciesService $service, $id) {
        if ($id && $service->deleteSubtype(Subtype::find($id))) {
            flash('Subtype deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/data/subtypes');
    }

    /**
     * Sorts subtypes.
     *
     * @param App\Services\SpeciesService $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSortSubtypes(Request $request, SpeciesService $service) {
        if ($service->sortSubtypes($request->get('sort'))) {
            flash('Subtype order updated successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }
}
