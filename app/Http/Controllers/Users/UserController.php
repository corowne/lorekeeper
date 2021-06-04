<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;

use DB;
use Auth;
use Route;
use App\Models\User\User;
use App\Models\User\UserCurrency;
use App\Models\Currency\Currency;
use App\Models\Currency\CurrencyLog;

use App\Models\User\UserItem;
use App\Models\Item\Item;
use App\Models\Item\ItemCategory;
use App\Models\Item\UserItemLog;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | User Controller
    |--------------------------------------------------------------------------
    |
    | Displays user profile pages.
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $name = Route::current()->parameter('name');
        $this->user = User::where('name', $name)->first();
        if(!$this->user) abort(404);

        $this->user->updateCharacters();
        $this->user->updateArtDesignCredits();
    }

    /**
     * Shows a user's profile.
     *
     * @param  string  $name
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUser($name)
    {
        return view('user.profile', [
            'user' => $this->user,
            'items' => $this->user->items()->orderBy('user_items.updated_at', 'DESC')->take(4)->get()
        ]);
    }
    
    /**
     * Shows a user's characters.
     *
     * @param  string  $name
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUserCharacters($name)
    {
        return view('user.characters', [
            'user' => $this->user,
            'characters' => $this->user->characters()->visible()->get()
        ]);
    }
    
    /**
     * Shows a user's MYO slots.
     *
     * @param  string  $name
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUserMyoSlots($name)
    {
        return view('user.myo_slots', [
            'user' => $this->user,
            'myos' => $this->user->myoSlots()->visible()->get()
        ]);
    }
    
    /**
     * Shows a user's inventory.
     *
     * @param  string  $name
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUserInventory($name)
    {
        $categories = ItemCategory::orderBy('sort', 'DESC')->get();
        $items = count($categories) ? $this->user->items()->orderByRaw('FIELD(item_category_id,'.implode(',', $categories->pluck('id')->toArray()).')')->orderBy('name')->orderBy('updated_at')->get()->groupBy('item_category_id') : $this->user->items()->orderBy('name')->orderBy('updated_at')->get()->groupBy('item_category_id');
        return view('user.inventory', [
            'user' => $this->user,
            'categories' => $categories->keyBy('id'),
            'items' => $items,
            'userOptions' => User::where('id', '!=', $this->user->id)->orderBy('name')->pluck('name', 'id')->toArray(),
            'user' => $this->user,
            'logs' => $this->user->getItemLogs()
        ]);
    }

    /**
     * Shows a user's profile.
     *
     * @param  string  $name
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
     * Shows a user's currency logs.
     *
     * @param  string  $name
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
     * Shows a user's item logs.
     *
     * @param  string  $name
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

    /**
     * Shows a user's character ownership logs.
     *
     * @param  string  $name
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUserOwnershipLogs($name)
    {
        return view('user.ownership_logs', [
            'user' => $this->user,
            'logs' => $this->user->getOwnershipLogs()
        ]);
    }

    /**
     * Shows a user's submissions.
     *
     * @param  string  $name
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUserSubmissions($name)
    {
        return view('user.submission_logs', [
            'user' => $this->user,
            'logs' => $this->user->getSubmissions()
        ]);
    }
}
