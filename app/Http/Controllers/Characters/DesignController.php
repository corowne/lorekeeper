<?php

namespace App\Http\Controllers\Characters;

use Illuminate\Http\Request;

use DB;
use Auth;
use Settings;
use App\Models\User\User;
use App\Models\User\UserItem;
use App\Models\Character\Character;
use App\Models\Character\CharacterDesignUpdate;
use App\Models\Species;
use App\Models\Rarity;
use App\Models\Feature\Feature;
use App\Models\Item\ItemCategory;
use App\Services\CharacterManager;

use App\Http\Controllers\Controller;

class DesignController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }
    
    /**
     * Show the index of character design update submissions.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDesignUpdateIndex($id)
    {
        return view('character.myo.character', [
            'character' => $this->character,
        ]);
    }

    /**
     * Show a design update request.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDesignUpdate($id)
    {
        $r = CharacterDesignUpdate::find($id);
        if(!$r || ($r->user_id != Auth::user()->id && !Auth::user()->hasPower('manage_characters'))) abort(404);
        return view('character.design.request', [
            'request' => $r
        ]);
    }

    /**
     * Show a design update request's comments section.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getComments($id)
    {
        $r = CharacterDesignUpdate::find($id);
        if(!$r || ($r->user_id != Auth::user()->id && !Auth::user()->hasPower('manage_characters'))) abort(404);
        return view('character.design.comments', [
            'request' => $r
        ]);
    }
    
    public function postComments(Request $request, CharacterManager $service, $id)
    {
        $r = CharacterDesignUpdate::find($id);
        if(!$r) abort(404);
        if($r->user_id != Auth::user()->id) abort(404);
        
        if($service->saveRequestComment($request->only(['comments']), $r)) {
            flash('Request edited successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Show a design update request's image section.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getImage($id)
    {
        $r = CharacterDesignUpdate::find($id);
        if(!$r || ($r->user_id != Auth::user()->id && !Auth::user()->hasPower('manage_characters'))) abort(404);
        return view('character.design.image', [
            'request' => $r
        ]);
    }
    
    public function postImage(Request $request, CharacterManager $service, $id)
    {
        $r = CharacterDesignUpdate::find($id);
        if(!$r) abort(404);
        if($r->user_id != Auth::user()->id) abort(404);
        $request->validate(CharacterDesignUpdate::$imageRules);
        
        if($service->saveRequestImage($request->all(), $r)) {
            flash('Request edited successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /**
     * Show a design update request's addons section.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getAddons($id)
    {
        $r = CharacterDesignUpdate::find($id);
        if(!$r || ($r->user_id != Auth::user()->id && !Auth::user()->hasPower('manage_characters'))) abort(404);
        return view('character.design.addons', [
            'request' => $r,
            'categories' => ItemCategory::orderBy('sort', 'DESC')->get(),
            'inventory' => UserItem::with('item')->whereNull('deleted_at')->where('user_id', Auth::user()->id)->where(function($query) use ($id) {
                $query->whereNull('holding_id')->orWhere(function($query) use ($id) {
                    $query->where('holding_type', 'Update')->where('holding_id', $id);
                });
            })->get()
        ]);
    }
    
    public function postAddons(Request $request, CharacterManager $service, $id)
    {
        $r = CharacterDesignUpdate::find($id);
        if(!$r) abort(404);
        if($r->user_id != Auth::user()->id) abort(404);
        
        if($service->saveRequestAddons($request->all(), $r)) {
            flash('Request edited successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Show a design update request's traits section.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getFeatures($id)
    {
        $r = CharacterDesignUpdate::find($id);
        if(!$r || ($r->user_id != Auth::user()->id && !Auth::user()->hasPower('manage_characters'))) abort(404);
        return view('character.design.features', [
            'request' => $r,
            'specieses' => ['0' => 'Select Species'] + Species::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'rarities' => ['0' => 'Select Rarity'] + Rarity::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'features' => Feature::orderBy('name')->pluck('name', 'id')->toArray(), // if MYO slot and rarity_id is set, only pull lower rarities
        ]);
    }
    
    public function postFeatures(Request $request, CharacterManager $service, $id)
    {
        $r = CharacterDesignUpdate::find($id);
        if(!$r) abort(404);
        if($r->user_id != Auth::user()->id) abort(404);
        
        if($service->saveRequestFeatures($request->all(), $r)) {
            flash('Request edited successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    
    public function postSubmit(CharacterManager $service, $id)
    {
        $r = CharacterDesignUpdate::find($id);
        if(!$r) abort(404);
        if($r->user_id != Auth::user()->id) abort(404);
        
        if($service->submitRequest($r)) {
            flash('Request submitted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

}
