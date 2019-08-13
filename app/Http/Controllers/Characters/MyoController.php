<?php

namespace App\Http\Controllers\Characters;

use Illuminate\Http\Request;

use DB;
use Auth;
use Route;
use Settings;
use App\Models\User\User;
use App\Models\Character\Character;
use App\Models\Currency\Currency;
use App\Models\Currency\CurrencyLog;
use App\Models\User\UserCurrency;
use App\Models\Character\CharacterCurrency;
use App\Models\Character\CharacterTransfer;

use App\Services\CurrencyManager;
use App\Services\CharacterManager;

use App\Http\Controllers\Controller;

class MyoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $id = Route::current()->parameter('id');
            $query = Character::myo(1)->where('id', $id);
            if(!(Auth::check() && Auth::user()->hasPower('manage_masterlist'))) $query->where('is_visible', 1);
            $this->character = $query->first();
            if(!$this->character) abort(404);

            $this->character->updateOwner();
            return $next($request);
        });
    }

    /**
     * Show a character's masterlist entry.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacter($id)
    {
        return view('character.myo.character', [
            'character' => $this->character,
        ]);
    }

    /**
     * Show a character's profile.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterProfile($id)
    {
        return view('character.profile', [
            'character' => $this->character,
        ]);
    }

    public function getEditCharacterProfile($id)
    {
        if(!Auth::check()) abort(404);
        
        $isMod = Auth::user()->hasPower('manage_characters');
        $isOwner = ($this->character->user_id == Auth::user()->id);
        if(!$isMod && !$isOwner) abort(404);

        return view('character.edit_profile', [
            'character' => $this->character,
        ]);
    }
    
    public function postEditCharacterProfile(Request $request, CharacterManager $service, $id)
    {
        if(!Auth::check()) abort(404);

        $isMod = Auth::user()->hasPower('manage_characters');
        $isOwner = ($this->character->user_id == Auth::user()->id);
        if(!$isMod && !$isOwner) abort(404);
        
        if($service->updateCharacterProfile($request->only(['text', 'is_gift_art_allowed', 'is_trading', 'alert_user']), $this->character, Auth::user(), !$isOwner)) {
            flash('Profile edited successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /**
     * Show a character's ownership logs.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterOwnershipLogs($id)
    {
        return view('character.ownership_logs', [
            'character' => $this->character,
            'logs' => $this->character->getOwnershipLogs(0)
        ]);
    }
    
    /**
     * Show a character's ownership logs.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterLogs($id)
    {
        return view('character.character_logs', [
            'character' => $this->character,
            'logs' => $this->character->getCharacterLogs()
        ]);
    }
    
    /**
     * Show a character's submissions.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterSubmissions($id)
    {
        return view('character.submission_logs', [
            'character' => $this->character,
            'logs' => $this->character->getSubmissions()
        ]);
    }

    

    public function getTransfer($id)
    {
        if(!Auth::check()) abort(404);
        
        $isMod = Auth::user()->hasPower('manage_characters');
        $isOwner = ($this->character->user_id == Auth::user()->id);
        if(!$isMod && !$isOwner) abort(404);

        return view('character.transfer', [
            'character' => $this->character,
            'transfer' => CharacterTransfer::active()->where('character_id', $this->character->id)->first(),
            'cooldown' => Settings::get('transfer_cooldown'),
            'transfersQueue' => Settings::get('open_transfers_queue'),
            'userOptions' => User::query()->orderBy('name')->pluck('name', 'id')->toArray(),
        ]);
    }
    
    public function postTransfer(Request $request, CharacterManager $service, $id)
    {
        if(!Auth::check()) abort(404);
        
        if($service->createTransfer($request->only(['recipient_id']), $this->character, Auth::user())) {
            flash('Transfer created successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    public function postCancelTransfer(Request $request, CharacterManager $service, $id, $id2)
    {
        if(!Auth::check()) abort(404);
        
        if($service->cancelTransfer(['transfer_id' => $id2], Auth::user())) {
            flash('Transfer cancelled.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

}
