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
    }

    /**
     * Show a user's profile.
     *
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
     * Show a user's characters.
     *
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
     * Show a user's currency logs.
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
     * Show a user's item logs.
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

    
    
    /**
     * Show a user's character ownership logs.
     *
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
     * Show a user's submissions.
     *
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
