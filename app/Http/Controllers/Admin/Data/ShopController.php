<?php

namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;

use Auth;

use App\Models\Shop\Shop;
use App\Models\Shop\ShopStock;
use App\Models\Item\Item;
use App\Models\Currency\Currency;

use App\Services\ShopService;

use App\Http\Controllers\Controller;

class ShopController extends Controller
{
    /**
     * Show the shop index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('admin.shops.shops', [
            'shops' => Shop::orderBy('sort', 'DESC')->get()
        ]);
    }
    
    /**
     * Show the create shop page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateShop()
    {
        return view('admin.shops.create_edit_shop', [
            'shop' => new Shop
        ]);
    }
    
    /**
     * Show the edit shop page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditShop($id)
    {
        $shop = Shop::find($id);
        if(!$shop) abort(404);
        return view('admin.shops.create_edit_shop', [
            'shop' => $shop,
            'items' => Item::orderBy('name')->pluck('name', 'id'),
            'currencies' => Currency::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function postCreateEditShop(Request $request, ShopService $service, $id = null)
    {
        $id ? $request->validate(Shop::$updateRules) : $request->validate(Shop::$createRules);
        $data = $request->only([
            'name', 'description', 'image', 'remove_image', 'is_active'
        ]);
        if($id && $service->updateShop(Shop::find($id), $data, Auth::user())) {
            flash('Shop updated successfully.')->success();
        }
        else if (!$id && $shop = $service->createShop($data, Auth::user())) {
            flash('Shop created successfully.')->success();
            return redirect()->to('admin/data/shops/edit/'.$shop->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    public function postEditShopStock(Request $request, ShopService $service, $id)
    {
        $data = $request->only([
            'shop_id', 'item_id', 'currency_id', 'cost', 'use_user_bank', 'use_character_bank', 'is_limited_stock', 'quantity', 'purchase_limit'
        ]);
        if($service->updateShopStock(Shop::find($id), $data, Auth::user())) {
            flash('Shop stock updated successfully.')->success();
            return redirect()->back();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /**
     * Get the shop deletion modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteShop($id)
    {
        $shop = Shop::find($id);
        return view('admin.items._delete_shop', [
            'shop' => $shop,
        ]);
    }

    public function postDeleteShop(Request $request, ShopService $service, $id)
    {
        if($id && $service->deleteShop(Shop::find($id))) {
            flash('Shop deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/shops');
    }

    

    public function postSortShop(Request $request, ShopService $service)
    {
        if($service->sortShop($request->get('sort'))) {
            flash('Shop order updated successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

}
