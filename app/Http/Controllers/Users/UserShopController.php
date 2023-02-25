<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;

use Auth;
use Route;

use App\Models\Shop\UserShop;
use App\Models\Shop\UserShopStock;
use App\Models\Item\Item;
use App\Models\Currency\Currency;
use App\Services\InventoryManager;


use App\Services\UserShopService;

use App\Http\Controllers\Controller;

class UserShopController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | User / Shop Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of shops and shop stock.
    |
    */

    /**
     * Shows the shop index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUserIndex()
    {
        return view('home.user_shops.my_shops', [
            'shops' => UserShop::orderBy('sort', 'DESC')->get()
        ]);
    }
    
    /**
     * Shows the create shop page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateShop()
    {
        return view('home.user_shops.create_edit_shop', [
            'shop' => new UserShop
        ]);
    }
    
    /**
     * Shows the edit shop page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditShop($id)
    {
        $shop = UserShop::find($id);
        if(!$shop) abort(404);
        return view('home.user_shops.create_edit_shop', [
            'shop' => $shop,
            'items' => Item::orderBy('name')->pluck('name', 'id'),
            'currencies' => Currency::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    /**
     * Creates or edits a shop.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\UserShopService  $service
     * @param  int|null                  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditShop(Request $request, UserShopService $service, $id = null)
    {
        $id ? $request->validate(UserShop::$updateRules) : $request->validate(UserShop::$createRules);
        $data = $request->only([
            'name', 'description', 'image', 'remove_image', 'is_active'
        ]);
        if($id && $service->updateShop(UserShop::find($id), $data, Auth::user())) {
            flash('Shop updated successfully.')->success();
        }
        else if (!$id && $shop = $service->createShop($data, Auth::user())) {
            flash('Shop created successfully.')->success();
            return redirect()->to('usershops/edit/'.$shop->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * loads the edit stock modal
     */
    public function getEditShopStock($id)
    {
        $stock = UserShopStock::find($id);
        if(!$stock) abort(404);

        return view('home.user_shops._edit_stock_modal', [
            'shop' => $stock->shop,
            'stock' => $stock,
            'currencies' => Currency::where('is_user_owned', 1)->where('allow_user_to_user', 1)->orderBy('name')->pluck('name', 'id'),
            'items' => Item::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    /**
     * Ajax function to return stock type
     */
    public function getShopStockType(Request $request)
    {
        $type = $request->input('type');
        if($type == 'Item') {
            return view('home.user_shops._stock_item', [
                'items' => Item::orderBy('name')->pluck('name', 'id')
            ]);
        }
    }

    /**
     * Edits a shop's stock.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\UserShopService  $service
     * @param  int                       $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEditShopStock(Request $request, UserShopService $service, $id)
    {
        $data = $request->only([
            'shop_id', 'item_id', 'currency_id', 'cost','stock_type','is_visible'
        ]);
        if($service->editShopStock(UserShopStock::find($id), $data, Auth::user())) {
            flash('Shop stock updated successfully.')->success();
            return redirect()->back();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Gets the stock deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getRemoveShopStock($id)
    {
        $stock = UserShopStock::find($id);
        return view('home.user_shops._delete_stock', [
            'stock' => $stock,
        ]);
    }
    
    /**
     * Gets the shop deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteShop($id)
    {
        $shop = UserShop::find($id);
        return view('home.user_shops._delete_shop', [
            'shop' => $shop,
        ]);
    }

    /**
     * Deletes a shop.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\UserShopService  $service
     * @param  int                       $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteShop(Request $request, UserShopService $service, $id)
    {
        if($id && $service->deleteShop(UserShop::find($id))) {
            flash('Shop deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('usershops');
    }

    /**
     * Sorts shops.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\UserShopService  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSortShop(Request $request, UserShopService $service)
    {
        if($service->sortShop($request->get('sort'))) {
            flash('Shop order updated successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }


    /**
     * Transfers inventory items back to a user.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\InventoryManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postRemoveStock(Request $request, InventoryManager $service)
    {   $recipient = Auth::user();
        $sender = $this->shop;
        if($service->sendShop($sender, $recipient, UserShopStock::find($request->get('ids')), $request->get('quantities'))) {
            flash('Item transferred successfully.')->success();
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
        return view('home.user_shops.purchase_history', [
            'logs' => Auth::user()->getUserShopLogs(0),
            'shops' => UserShop::where('is_active', 1)->orderBy('sort', 'DESC')->get(),
        ]);
    }

    /**
     * Show the item search page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getItemSearch(Request $request)
    { 
        $item = Item::find($request->only(['item_id']))->first();

        if($item) {
            // Gather all instances of this item
            $shopItems = UserShopStock::where('item_id', $item->id)->where('is_visible', 1)->where('quantity', '>', 0)->get();
            $shops = UserShop::whereIn('id', $shopItems->pluck('shop_id')->toArray())->orderBy('name', 'ASC')->get();
        }

        return view('home.user_shops.search_items', [
            'item' => $item ? $item : null,
            'items' => Item::orderBy('name')->pluck('name', 'id'),
            'shopItems' => $item ? $shopItems : null, 
            'shops' => $item ? $shops : null,
        ]);
    }

}
