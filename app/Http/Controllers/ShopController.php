<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Services\ShopManager;

use App\Models\Shop\Shop;
use App\Models\Shop\ShopStock;
use App\Models\Shop\ShopLog;
use App\Models\Item\Item;
use App\Models\Currency\Currency;
use App\Models\Item\ItemCategory;

class ShopController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Shop Controller
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
        return view('shops.index', [
            'shops' => Shop::where('is_active', 1)->orderBy('sort', 'DESC')->get()
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
        $shop = Shop::where('id', $id)->where('is_active', 1)->first();
        if(!$shop) abort(404);
        $items = count($categories) ? $shop->displayStock()->orderByRaw('FIELD(item_category_id,'.implode(',', $categories->pluck('id')->toArray()).')')->orderBy('name')->get()->groupBy('item_category_id') : $shop->displayStock()->orderBy('name')->get()->groupBy('item_category_id');
        return view('shops.shop', [
            'shop' => $shop,
            'categories' => $categories->keyBy('id'),
            'items' => $items,
            'shops' => Shop::where('is_active', 1)->orderBy('sort', 'DESC')->get(),
            'currencies' => Currency::whereIn('id', ShopStock::where('shop_id', $shop->id)->pluck('currency_id')->toArray())->get()->keyBy('id')
        ]);
    }

    /**
     * Gets the shop stock modal.
     *
     * @param  App\Services\ShopManager  $service
     * @param  int                       $id
     * @param  int                       $stockId
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getShopStock(ShopManager $service, $id, $stockId)
    {
        $shop = Shop::where('id', $id)->where('is_active', 1)->first();
        if(!$shop) abort(404);

        $stock = ShopStock::with('item')->where('id', $stockId)->where('shop_id', $id)->first();

        if(Auth::user()){
            $purchaseLimitReached = $service->checkPurchaseLimitReached($stock, Auth::user());
        } else $purchaseLimitReached = false;

        return view('shops._stock_modal', [
            'shop' => $shop,
            'stock' => $stock,
            'purchaseLimitReached' => $purchaseLimitReached
        ]);
    }

    /**
     * Buys an item from a shop.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\ShopManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postBuy(Request $request, ShopManager $service)
    {
        $request->validate(ShopLog::$createRules);
        if($service->buyStock($request->only(['stock_id', 'shop_id', 'slug', 'bank']), Auth::user())) {
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
        return view('shops.purchase_history', [
            'logs' => Auth::user()->getShopLogs(0),
            'shops' => Shop::where('is_active', 1)->orderBy('sort', 'DESC')->get(),
        ]);
    }
}


