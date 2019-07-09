<?php

namespace App\Http\Controllers\Admin\Characters;

use Illuminate\Http\Request;

use Auth;
use Config;
use Settings;

use App\Models\Character\Character;
use App\Models\Character\CharacterCategory;
use App\Models\Rarity;
use App\Models\User\User;
use App\Models\Species;
use App\Models\Feature\Feature;
use App\Models\Character\CharacterTransfer;

use App\Services\CharacterManager;
use App\Services\CurrencyManager;

use App\Http\Controllers\Controller;

class CharacterController extends Controller
{
    /**
     * Show the create character page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateCharacter()
    {
        return view('admin.masterlist.create_character', [
            'categories' => CharacterCategory::orderBy('sort')->get(),
            'userOptions' => User::query()->orderBy('name')->pluck('name', 'id')->toArray(),
            'rarities' => ['0' => 'Select Rarity'] + Rarity::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'specieses' => ['0' => 'Select Species'] + Species::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'features' => Feature::orderBy('name')->pluck('name', 'id')->toArray()
        ]);
    }
    
    public function getPullNumber(CharacterManager $service, Request $request)
    {
        return $service->pullNumber($request->get('category'));
    }

    public function postCreateCharacter(Request $request, CharacterManager $service)
    {
        $request->validate(Character::$createRules);
        $data = $request->only([
            'user_id', 'owner_alias', 'character_category_id', 'number', 'slug',
            'description', 'is_visible', 'is_giftable', 'is_tradeable', 'is_sellable',
            'sale_value', 'transferrable_at', 'use_cropper',
            'x0', 'x1', 'y0', 'y1',
            'designer_alias', 'designer_url',
            'artist_alias', 'artist_url',
            'species_id', 'rarity_id', 'feature_id', 'feature_data',
            'image', 'thumbnail', 'image_description'
        ]);
        if ($character = $service->createCharacter($data, Auth::user())) {
            flash('Character created successfully.')->success();
            return redirect()->to($character->url);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }


    /**
     * Show the edit character stats modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditCharacterStats($slug)
    {
        $this->character = Character::where('slug', $slug)->first();
        if(!$this->character) abort(404);

        return view('character.admin._edit_stats_modal', [
            'character' => $this->character,
            'categories' => CharacterCategory::orderBy('sort')->pluck('name', 'id')->toArray(),
            'userOptions' => User::query()->orderBy('name')->pluck('name', 'id')->toArray(),
            'number' => format_masterlist_number($this->character->number, Config::get('lorekeeper.settings.character_number_digits'))
        ]);
    }

    public function postEditCharacterStats(Request $request, CharacterManager $service, $slug)
    {
        $request->validate(Character::$updateRules);
        $data = $request->only([
            'character_category_id', 'number', 'slug',
            'is_giftable', 'is_tradeable', 'is_sellable', 'sale_value',
            'transferrable_at'
        ]);
        $this->character = Character::where('slug', $slug)->first();
        if(!$this->character) abort(404);
        if ($service->updateCharacterStats($data, $this->character, Auth::user())) {
            flash('Character stats updated successfully.')->success();
            return redirect()->to($this->character->url);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Show the edit character description modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditCharacterDescription($slug)
    {
        $this->character = Character::where('slug', $slug)->first();
        if(!$this->character) abort(404);
        
        return view('character.admin._edit_description_modal', [
            'character' => $this->character,
        ]);
    }

    public function postEditCharacterDescription(Request $request, CharacterManager $service, $slug)
    {
        $data = $request->only([
            'description'
        ]);
        $this->character = Character::where('slug', $slug)->first();
        if(!$this->character) abort(404);
        if ($service->updateCharacterDescription($data, $this->character, Auth::user())) {
            flash('Character description updated successfully.')->success();
            return redirect()->to($this->character->url);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    public function postCharacterSettings(Request $request, CharacterManager $service, $slug)
    {
        $data = $request->only([
            'is_visible'
        ]);
        $this->character = Character::where('slug', $slug)->first();
        if(!$this->character) abort(404);
        if ($service->updateCharacterSettings($data, $this->character, Auth::user())) {
            flash('Character settings updated successfully.')->success();
            return redirect()->to($this->character->url);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Show the delete character modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterDelete($slug)
    {
        $this->character = Character::where('slug', $slug)->first();
        if(!$this->character) abort(404);

        return view('character.admin._delete_character_modal', [
            'character' => $this->character,
        ]);
    }

    public function postCharacterDelete(Request $request, CharacterManager $service, $slug)
    {
        $this->character = Character::where('slug', $slug)->first();
        if(!$this->character) abort(404);

        if ($service->deleteCharacter($this->character, Auth::user())) {
            flash('Character deleted successfully.')->success();
            return redirect()->to($character->url);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    
    public function postTransfer(Request $request, CharacterManager $service, $slug)
    {
        $this->character = Character::where('slug', $slug)->first();
        if(!$this->character) abort(404);
        
        if($service->adminTransfer($request->only(['recipient_id', 'cooldown', 'reason']), $this->character, Auth::user())) {
            flash('Character transferred successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Show the character transfer queue.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getTransferQueue($type)
    {
        $transfers = CharacterTransfer::query();
        $user = Auth::user();

        if($type == 'completed') $transfers->completed();
        else if ($type == 'incoming') $transfers->active()->where('is_approved', 0);
        else abort(404);

        return view('admin.masterlist.character_transfers', [
            'transfers' => $transfers->orderBy('id', 'DESC')->paginate(20),
            'transfersQueue' => Settings::get('open_transfers_queue'),
        ]);
    }
    
    /**
     * Show the character transfer action modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getTransferModal($id, $action)
    {
        if($action != 'approve' && $action != 'reject') abort(404);
        $transfer = CharacterTransfer::where('id', $id)->active()->first();
        if(!$transfer) abort(404);

        return view('admin.masterlist._'.$action.'_modal', [
            'transfer' => $transfer,
            'cooldown' => Settings::get('transfer_cooldown')
        ]);
    }
    
    public function postTransferQueue(Request $request, CharacterManager $service, $id)
    {
        if(!Auth::check()) abort(404);

        $action = $request->get('action');
        
        if($service->processTransferQueue($request->only(['action', 'cooldown', 'reason']) + ['transfer_id' => $id], Auth::user())) {
            flash('Transfer ' . strtolower($action) . 'ed.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
}
