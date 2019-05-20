<?php

namespace App\Http\Controllers\Characters;

use Illuminate\Http\Request;

use DB;
use Auth;
use Route;
use App\Models\Character\Character;

use App\Http\Controllers\Controller;

class CharacterController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $slug = Route::current()->parameter('slug');
        $this->character = Character::where('slug', $slug)->first();
        if(!$this->character) abort(404);

        $this->character->updateOwner();
    }

    /**
     * Show a character's masterlist entry.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacter($slug)
    {
        return view('character.character', [
            'character' => $this->character,
        ]);
    }

    /**
     * Show a character's profile.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterProfile($slug)
    {
        return view('character.profile', [
            'character' => $this->character,
        ]);
    }
    
    /**
     * Show a user's characters.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUserCharacters($name)
    {
        return view('user.characters', [
            'user' => $this->user
        ]);
    }
    
    /**
     * Show a user's inventory.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUserInventory($name)
    {
        return view('user.inventory', [
            'user' => $this->user,
            'categories' => ItemCategory::orderBy('sort', 'DESC')->get()->keyBy('id'),
            'items' => $this->user->items()->orderBy('name')->orderBy('updated_at')->get()->groupBy('item_category_id'),
            'userOptions' => User::where('id', '!=', $this->user->id)->orderBy('name')->pluck('name', 'id')->toArray(),
            'user' => $this->user,
            'logs' => $this->user->getItemLogs()
        ]);
    }

    
    /**
     * Show a user's profile.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUserBank($name)
    {
        $user = $this->user;
        return view('user.bank', [
            'user' => $this->user,
            'logs' => $this->user->getCurrencyLogs(),
        ] + (Auth::check() && Auth::user()->id == $this->user->id ? [
            'currencyOptions' => Currency::where('allow_user_to_user', 1)->where('is_user_owned', 1)->whereIn('id', UserCurrency::where('user_id', $this->user->id)->pluck('currency_id')->toArray())->orderBy('sort_user', 'DESC')->pluck('name', 'id')->toArray(),
            'userOptions' => User::where('id', '!=', Auth::user()->id)->orderBy('name')->pluck('name', 'id')->toArray()

        ] : []));
    }

    
    /**
     * Show a user's profile.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUserCurrencyLogs($name)
    {
        $user = $this->user;
        return view('user.currency_logs', [
            'user' => $this->user,
            'logs' => $this->user->getCurrencyLogs(0)
        ]);
    }

    
    /**
     * Show a user's profile.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUserItemLogs($name)
    {
        $user = $this->user;
        return view('user.item_logs', [
            'user' => $this->user,
            'logs' => $this->user->getItemLogs(0)
        ]);
    }

}
