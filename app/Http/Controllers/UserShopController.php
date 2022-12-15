<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Services\UserShopManager;

use App\Models\Shop\UserShop;
use App\Models\Shop\UserShopStock;
use App\Models\Shop\UserShopLog;
use App\Models\Item\Item;
use App\Models\Currency\Currency;
use App\Models\Item\ItemCategory;
use App\Models\User\UserItem;

class UserShopController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | User Shop Controller
    |--------------------------------------------------------------------------
    |
    | Handles viewing the shop index, shops and purchasing from shops.
    |
    */

    /**
     * Shows the shop index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('home.index_shop', [
            'shops' => UserShop::where('is_active', 1)->orderBy('sort', 'DESC')->get()
            ]);
    }

    /**
     * Shows a shop.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getShop($id)
    {
        $categories = ItemCategory::orderBy('sort', 'DESC')->get();
        $shop = UserShop::where('id', $id)->where('is_active', 1)->first();
        if(!$shop) abort(404);
        $items = count($categories) ? $shop->displayStock()->orderByRaw('FIELD(item_category_id,'.implode(',', $categories->pluck('id')->toArray()).')')->orderBy('name')->get()->groupBy('item_category_id') : $shop->displayStock()->orderBy('name')->get()->groupBy('item_category_id');
        return view('user.shop', [
            'shop' => $shop,
            'categories' => $categories->keyBy('id'),
            'items' => $items,
            'shops' => UserShop::where('is_active', 1)->orderBy('sort', 'DESC')->get(),
            'currencies' => Currency::whereIn('id', UserShopStock::where('shop_id', $shop->id)->pluck('currency_id')->toArray())->get()->keyBy('id')
        ]);
    }

    /**
     * Gets the shop stock modal.
     *
     * @param  App\Services\UserShopManager  $service
     * @param  int                       $id
     * @param  int                       $stockId
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getShopStock(UserShopManager $service, $id, $stockId)
    {
        $shop = UserShop::where('id', $id)->where('is_active', 1)->first();
        $stock = UserShopStock::with('item')->where('id', $stockId)->where('shop_id', $id)->first();

        $user = Auth::user();
        if($user){
            $userOwned = UserItem::where('user_id', $user->id)->where('item_id', $stock->item->id)->where('count', '>', 0)->get();
        }

        if(!$shop) abort(404);
        return view('home._stock_modal', [
            'shop' => $shop,
            'stock' => $stock,
            'userOwned' => $user ? $userOwned : null
		]);
    }

    /**
     * Buys an item from a shop.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\UserShopManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postBuy(Request $request, UserShopManager $service)
    {
        $request->validate(UserShopLog::$createRules);
        if($service->buyStock($request->only(['stock_id', 'shop_id', 'slug', 'bank', 'quantity']), Auth::user())) {
            flash('Successfully purchased item.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Shows the user's purchase history.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getPurchaseHistory()
    {
        return view('home.purchase_history', [
            'logs' => Auth::user()->getShopLogs(0),
            'shops' => UserShop::where('is_active', 1)->orderBy('sort', 'DESC')->get(),
        ]);
    }
}


