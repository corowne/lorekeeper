<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Services\UserShopManager;

use App\Models\Shop\UserShop;
use App\Models\Shop\UserShopStock;
use App\Models\Item\Item;
use App\Models\Currency\Currency;
use App\Models\Item\ItemCategory;
use App\Models\User\UserItem;
use App\Models\Shop\UserShopLog;

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
     * Shows the user list.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex(Request $request)
    {
        $query = UserShop::visible(Auth::check() ? Auth::user() : null);
        $sort = $request->only(['sort']);

        if($request->get('name')) $query->where(function($query) use ($request) {
            $query->where('name', 'LIKE', '%' . $request->get('name') . '%');
        }); 

        switch(isset($sort['sort']) ? $sort['sort'] : null) {
            default:
                $query->inRandomOrder();
                break;
            case 'alpha':
                $query->orderBy('name');
                break;
            case 'alpha-reverse':
                $query->orderBy('name', 'DESC');
                break;
            case 'newest':
                $query->orderBy('id', 'DESC');
                break;
            case 'oldest':
                $query->orderBy('id', 'ASC');
                break;
            case 'update':
                $query->orderBy('updated_at', 'DESC');
                break;
            case 'update-reverse':
                $query->orderBy('updated_at', 'ASC');
                break;
        }

        return view('home.user_shops.index_shop', [
            'shops' => $query->paginate(30)->appends($request->query()), 
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
        $shop = UserShop::where('id', $id)->first();
        if($shop->is_active != 1 && !Auth::user()->hasPower('edit_inventories')) abort(404);
        $items = count($categories) ? $shop->displayStock()->orderByRaw('FIELD(item_category_id,'.implode(',', $categories->pluck('id')->toArray()).')')->orderBy('name')->get()->groupBy('item_category_id') : $shop->displayStock()->orderBy('name')->get()->groupBy('item_category_id');
        return view('home.user_shops.shop', [
            'shop' => $shop,
            'categories' => $categories->keyBy('id'),
            'items' => $items,
            'shops' => UserShop::where('is_active', 1)->orderBy('sort', 'DESC')->get(),
            'currencies' => Currency::whereIn('id', UserShopStock::where('user_shop_id', $shop->id)->pluck('currency_id')->toArray())->get()->keyBy('id')
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
        $stock = UserShopStock::with('item')->where('id', $stockId)->where('user_shop_id', $id)->first();

        $user = Auth::user();
        if($user){
            $userOwned = UserItem::where('user_id', $user->id)->where('item_id', $stock->item->id)->where('count', '>', 0)->get();
        }

        if(!$shop) abort(404);
        return view('home.user_shops._stock_modal', [
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
        if($service->buyStock($request->only(['stock_id', 'user_shop_id', 'bank', 'quantity']), Auth::user())) {
            flash('Successfully purchased item.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

}


