<?php

namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;

use Auth;

use App\Models\Rarity;
use App\Models\Character\CharacterLineageBlacklist;

use App\Services\RarityService;

use App\Http\Controllers\Controller;

class RarityController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin / Rarity Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of rarities.
    |
    */

    /**
     * Shows the rarity index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('admin.rarities.rarities', [
            'rarities' => Rarity::orderBy('sort', 'DESC')->get()
        ]);
    }
    
    /**
     * Shows the create rarity page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateRarity()
    {
        return view('admin.rarities.create_edit_rarity', [
            'lineageBlacklist' => null,
            'rarity' => new Rarity
        ]);
    }
    
    /**
     * Shows the edit rarity page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditRarity($id)
    {
        $rarity = Rarity::find($id);
        if(!$rarity) abort(404);
        return view('admin.rarities.create_edit_rarity', [
            'lineageBlacklist' => CharacterLineageBlacklist::where('type', 'rarity')->where('type_id', $id)->get()->first(),
            'rarity' => $rarity
        ]);
    }

    /**
     * Creates or edits a rarity.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  App\Services\RarityService  $service
     * @param  int|null                    $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditRarity(Request $request, RarityService $service, $id = null)
    {
        $id ? $request->validate(Rarity::$updateRules) : $request->validate(Rarity::$createRules);
        $data = $request->only([
            'lineage-blacklist',
            'name', 'color', 'description', 'image', 'remove_image'
        ]);
        if($id && $service->updateRarity(Rarity::find($id), $data, Auth::user())) {
            flash('Rarity updated successfully.')->success();
        }
        else if (!$id && $rarity = $service->createRarity($data, Auth::user())) {
            flash('Rarity created successfully.')->success();
            return redirect()->to('admin/data/rarities/edit/'.$rarity->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /**
     * Gets the rarity deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteRarity($id)
    {
        $rarity = Rarity::find($id);
        return view('admin.rarities._delete_rarity', [
            'rarity' => $rarity,
        ]);
    }

    /**
     * Deletes a rarity.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  App\Services\RarityService  $service
     * @param  int                         $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteRarity(Request $request, RarityService $service, $id)
    {
        if($id && $service->deleteRarity(Rarity::find($id))) {
            flash('Rarity deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/rarities');
    }

    /**
     * Sorts rarities.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  App\Services\RarityService  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSortRarity(Request $request, RarityService $service)
    {
        if($service->sortRarity($request->get('sort'))) {
            flash('Rarity order updated successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
}
