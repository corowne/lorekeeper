<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Shop\Shop;
use App\Models\Shop\ShopStock;
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
            'shops' => Shop::where('is_active', 1)->orderBy('sort', 'DESC')->get()
        ]);
    }
}


