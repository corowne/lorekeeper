<?php

namespace App\Http\Controllers\Admin\Users;

use Auth;
use Config;
use Illuminate\Http\Request;

use App\Models\User\User;
use App\Models\Item\Item;
use App\Models\Currency\Currency;

use App\Models\User\UserItem;
use App\Models\Character\CharacterItem;
use App\Models\Trade;
use App\Models\Character\CharacterDesignUpdate;
use App\Models\Submission\Submission;

use App\Models\Character\Character;
use App\Services\CurrencyManager;
use App\Services\InventoryManager;
use App\Services\Stat\ExperienceManager;

use App\Http\Controllers\Controller;

class GrantController extends Controller
{
    /**
     * Show the currency grant page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUserCurrency()
    {
        return view('admin.grants.user_currency', [
            'users' => User::orderBy('id')->pluck('name', 'id'),
            'userCurrencies' => Currency::where('is_user_owned', 1)->orderBy('sort_user', 'DESC')->pluck('name', 'id')
        ]);
    }

    /**
     * Grants or removes currency from multiple users.
     *
     * @param  \Illuminate\Http\Request      $request
     * @param  App\Services\CurrencyManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postUserCurrency(Request $request, CurrencyManager $service)
    {
        $data = $request->only(['names', 'currency_id', 'quantity', 'data']);
        if($service->grantUserCurrencies($data, Auth::user())) {
            flash('Currency granted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Show the item grant page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getItems()
    {
        return view('admin.grants.items', [
            'users' => User::orderBy('id')->pluck('name', 'id'),
            'items' => Item::orderBy('name')->pluck('name', 'id')
        ]);
    }

    /**
     * Grants or removes items from multiple users.
     *
     * @param  \Illuminate\Http\Request        $request
     * @param  App\Services\InventoryManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postItems(Request $request, InventoryManager $service)
    {
        $data = $request->only(['names', 'item_ids', 'quantities', 'data', 'disallow_transfer', 'notes']);
        if($service->grantItems($data, Auth::user())) {
            flash('Items granted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Grants or removes exp (show)
     */
    public function getExp()
    {
        return view('admin.grants.exp', [
            'users' => User::orderBy('id')->pluck('name', 'id'),
        ]);
    }

    /**
     * Grants or removes exp
     */
    public function postExp(Request $request, ExperienceManager $service)
    {
        $data = $request->only(['names', 'quantity', 'data']);
        if($service->grantExp($data, Auth::user())) {
            flash('EXP granted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /*
     * Show the item search page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getItemSearch(Request $request)
    {
        $item = Item::find($request->only(['item_id']))->first();

        if($item) {
            // Gather all instances of this item
            $userItems = UserItem::where('item_id', $item->id)->where('count', '>', 0)->get();
            $characterItems = CharacterItem::where('item_id', $item->id)->where('count', '>', 0)->get();

            // Gather the users and characters that own them
            $users = User::whereIn('id', $userItems->pluck('user_id')->toArray())->orderBy('name', 'ASC')->get();
            $characters = Character::whereIn('id', $characterItems->pluck('character_id')->toArray())->orderBy('slug', 'ASC')->get();

            // Gather hold locations
            $designUpdates = CharacterDesignUpdate::whereIn('user_id', $userItems->pluck('user_id')->toArray())->whereNotNull('data')->get();
            $trades = Trade::whereIn('sender_id', $userItems->pluck('user_id')->toArray())->orWhereIn('recipient_id', $userItems->pluck('user_id')->toArray())->get();
            $submissions = Submission::whereIn('user_id', $userItems->pluck('user_id')->toArray())->whereNotNull('data')->get();
        }

        return view('admin.grants.item_search', [
            'item' => $item ? $item : null,
            'items' => Item::orderBy('name')->pluck('name', 'id'),
            'userItems' => $item ? $userItems : null,
            'characterItems' => $item ? $characterItems : null,
            'users' => $item ? $users : null,
            'characters' => $item ? $characters : null,
            'designUpdates' => $item ? $designUpdates :null,
            'trades' => $item ? $trades : null,
            'submissions' => $item ? $submissions : null,
        ]);
    }
}
