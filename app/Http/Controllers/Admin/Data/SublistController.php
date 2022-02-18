<?php

namespace App\Http\Controllers\Admin\Data;

use App\Http\Controllers\Controller;
use App\Models\Character\CharacterCategory;
use App\Models\Character\Sublist;
use App\Models\Species\Species;
use App\Services\SublistService;
use Illuminate\Http\Request;

class SublistController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin / Masterlist Sub Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of sub masterlists.
    |
    */

    /**
     * Shows the sub masterlist index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('admin.sublist.sublist', [
            'sublists' => Sublist::orderBy('sort', 'DESC')->get(),
        ]);
    }

    /**
     * Shows the create sublist page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateSublist()
    {
        return view('admin.sublist.create_edit_sublist', [
            'sublist'       => new Sublist,
            'subCategories' => [],
            'subSpecies'    => [],
            'categories'    => CharacterCategory::orderBy('sort')->pluck('name', 'id'),
            'species'       => Species::orderBy('sort')->pluck('name', 'id'),
        ]);
    }

    /**
     * Shows the edit sublist page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditSublist($id)
    {
        $sublist = Sublist::find($id);
        if (!$sublist) {
            abort(404);
        }

        return view('admin.sublist.create_edit_sublist', [
            'sublist'       => $sublist,
            'subCategories' => CharacterCategory::where('masterlist_sub_id', $sublist->id)->pluck('id'),
            'subSpecies'    => Species::where('masterlist_sub_id', $sublist->id)->pluck('id'),
            'categories'    => CharacterCategory::orderBy('sort')->pluck('name', 'id'),
            'species'       => Species::orderBy('sort')->pluck('name', 'id'),
        ]);
    }

    /**
     * Creates or edits a sublist.
     *
     * @param App\Services\SublistService $service
     * @param int|null                    $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditSublist(Request $request, SublistService $service, $id = null)
    {
        if (!$request['show_main'] || $request['show_main'] == null) {
            $request['show_main'] = 0;
        } else {
            $request['show_main'] = 1;
        }
        $id ? $request->validate(Sublist::$updateRules) : $request->validate(Sublist::$createRules);
        $data = $request->only([
            'name', 'key', 'show_main',
        ]);
        $contents = $request->only(['categories', 'species']);

        if ($id && $service->updateSublist(Sublist::find($id), $data, $contents)) {
            flash('Sublist updated successfully.')->success();
        } elseif (!$id && $sublist = $service->createSublist($data, $contents)) {
            flash('Sublist created successfully.')->success();

            return redirect()->to('admin/data/sublists/edit/'.$sublist->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the sublist deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteSublist($id)
    {
        $sublist = Sublist::find($id);

        return view('admin.sublist._delete_sublist', [
            'sublist' => $sublist,
        ]);
    }

    /**
     * Deletes a sublist.
     *
     * @param App\Services\SublistService $service
     * @param int                         $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteSublist(Request $request, SublistService $service, $id)
    {
        if ($id && $service->deleteSublist(Sublist::find($id))) {
            flash('Sublist deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/data/sublists');
    }

    /**
     * Sorts sublist order.
     *
     * @param App\Services\SublistService $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSortSublist(Request $request, SublistService $service)
    {
        if ($service->sortSublist($request->get('sort'))) {
            flash('Category order updated successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }
}
