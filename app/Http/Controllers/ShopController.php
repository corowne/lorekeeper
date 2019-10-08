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
    /**
     * Show the shop index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('shops.index', [
            'shops' => Shop::where('is_active', 1)->orderBy('sort', 'DESC')->get()
            ]);
    }
    
    public function getShop($id)
    {
        $shop = Shop::where('id', $id)->where('is_active', 1)->first();
        if(!$shop) abort(404);
        return view('shops.shop', [
            'shop' => $shop,
            'categories' => ItemCategory::orderBy('sort', 'DESC')->get()->keyBy('id'),
            'items' => $shop->displayStock()->orderBy('name')->get()->groupBy('item_category_id'),
            'shops' => Shop::where('is_active', 1)->orderBy('sort', 'DESC')->get(),
            'currencies' => Currency::whereIn('id', ShopStock::where('shop_id', $shop->id)->pluck('currency_id')->toArray())->get()->keyBy('id')
        ]);
    }

    public function getShopStock(ShopManager $service, $id, $stockId)
    {
        $shop = Shop::where('id', $id)->where('is_active', 1)->first();
        if(!$shop) abort(404);
        return view('shops._stock_modal', [
            'shop' => $shop,
            'stock' => $stock = ShopStock::with('item')->where('id', $stockId)->where('shop_id', $id)->first(),
            'purchaseLimitReached' => $service->checkPurchaseLimitReached($stock, Auth::user())
        ]);
    }

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
     * Show the user's purchase history.
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


