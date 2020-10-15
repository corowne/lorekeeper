<?php

namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;

use Auth;

use App\Models\ScavengerHunt\ScavengerHunt;
use App\Models\ScavengerHunt\HuntTarget;
use App\Models\ScavengerHunt\HuntParticipant;
use App\Models\Item\Item;

use App\Services\HuntService;

use App\Http\Controllers\Controller;

class HuntController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin / ScavengerHunt Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of scavenger hunts and targets.
    |
    */

    /**
     * Shows the scavenger hunt index.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getHuntIndex(Request $request)
    {
        return view('admin.hunts.index', [
            'hunts' => ScavengerHunt::orderBy('start_at', 'DESC')->paginate(20)
        ]);
    }
    
    /**
     * Shows the create hunt page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateHunt()
    {
        return view('admin.hunts.create_edit_hunt', [
            'hunt' => new ScavengerHunt,
        ]);
    }
    
    /**
     * Shows the edit hunt page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditHunt($id)
    {
        $hunt = ScavengerHunt::find($id);
        if(!$hunt) abort(404);
        $participants = HuntParticipant::where('hunt_id', $id)->select('scavenger_participants.*')->join('users', 'users.id', '=', 'scavenger_participants.user_id')
        ->orderBy('users.name');
        
        return view('admin.hunts.create_edit_hunt', [
            'hunt' => $hunt,
            'participants' => $participants->paginate(20),
            'items' => Item::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    /**
     * Creates or edits a scavenger hunt.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  App\Services\PromptService  $service
     * @param  int|null                    $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditHunt(Request $request, HuntService $service, $id = null)
    {
        $id ? $request->validate(ScavengerHunt::$updateRules) : $request->validate(ScavengerHunt::$createRules);
        $data = $request->only([
            'name', 'display_name', 'summary', 'clue', 'locations', 'start_at', 'end_at'
        ]);
        if($id && $service->updateHunt(ScavengerHunt::find($id), $data, Auth::user())) {
            flash('Scavenger hunt updated successfully.')->success();
        }
        else if (!$id && $hunt = $service->createHunt($data, Auth::user())) {
            flash('Scavenger hunt created successfully.')->success();
            return redirect()->to('admin/data/hunts/edit/'.$hunt->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /**
     * Gets the hunt deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteHunt($id)
    {
        $hunt = ScavengerHunt::find($id);
        return view('admin.hunts._delete_hunt', [
            'hunt' => $hunt,
        ]);
    }

    /**
     * Deletes a prompt.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  App\Services\HuntService    $service
     * @param  int                         $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteHunt(Request $request, HuntService $service, $id)
    {
        if($id && $service->deleteHunt(ScavengerHunt::find($id))) {
            flash('Scavenger hunt deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/hunts');
    }

    /**********************************************************************************************
    
        HUNT TARGETS

    **********************************************************************************************/

    /**
     * Gets the target creation page.
     *
     * @param  App\Services\HuntService  $service
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateHuntTarget(HuntService $service, $id)
    {
        $hunt = ScavengerHunt::find($id);
        return view('admin.hunts.create_edit_target', [
            'hunt' => $hunt,
            'target' => new HuntTarget,
            'items' => Item::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    /**
     * Gets the target edit page.
     *
     * @param  App\Services\HuntService  $service
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditHuntTarget(HuntService $service, $id)
    {
        $target = HuntTarget::find($id);
        if(!$target) abort(404);
        return view('admin.hunts.create_edit_target', [
            'target' => $target,
            'hunt' => ScavengerHunt::find($target->hunt_id),
            'item' => Item::find($target->item_id),
            'items' => Item::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    /**
     * Creates and updates hunt targets.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\HuntService  $service
     * @param  int                       $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditHuntTarget(Request $request, HuntService $service, $id = null)
    {
        $id ? $request->validate(HuntTarget::$updateRules) : $request->validate(HuntTarget::$createRules);

        $data = $request->only([
            'item_id', 'quantity', 'hunt_id', 'description'
        ]);

        $hunt = $id ? ScavengerHunt::find(HuntTarget::find($id)->hunt_id) : ScavengerHunt::find($request->hunt_id);
        if(!$hunt) throw new \Exception ('No valid scavenger hunt found!');
        
        if($id && $service->updateTarget(HuntTarget::find($id), $data, Auth::user())) {
            flash('Target updated successfully.')->success();
        }
        else if (!$id && $target = $service->createTarget($data, Auth::user())) {
            flash('Target created successfully.')->success();
            return redirect()->to('admin/data/hunts/targets/edit/'.$target->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();

    }

    /**
     * Gets the target deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteHuntTarget($id)
    {
        $target = HuntTarget::find($id);
        return view('admin.hunts._delete_target', [
            'target' => $target,
        ]);
    }

    /**
     * Deletes a target.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  App\Services\HuntService    $service
     * @param  int                         $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteHuntTarget(Request $request, HuntService $service, $id)
    {
        if($id && $service->deleteTarget(HuntTarget::find($id))) {
            flash('Target deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/hunts');
    }

}
