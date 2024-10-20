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
use App\Models\Item\ItemCategory;


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
            'shops' => UserShop::where('user_id', Auth::user()->id)->orderBy('sort', 'DESC')->get()
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
        if($shop->user_id != Auth::user()->id && !Auth::user()->hasPower('edit_inventories')) abort(404);
        return view('home.user_shops.create_edit_shop', [
            'shop' => $shop,
            'items' => Item::orderBy('name')->pluck('name', 'id'),
            'currencies' => Currency::where('is_user_owned', 1)->where('allow_user_to_user', 1)->orderBy('name')->pluck('name', 'id'),
            'stocks' => UserShopStock::where('user_shop_id', $shop->id)->where('quantity', '>', 0)->orderBy('id', 'DESC')->paginate(30),
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
            return redirect()->to('user-shops/edit/'.$shop->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
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
            'shop_id', 'currency_id', 'cost','is_visible'
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
        return redirect()->to('user-shops');
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
     * Shows the user's purchase history.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getPurchaseHistory()
    {
        return view('home.user_shops.purchase_history', [
            'logs' => Auth::user()->getUserShopLogs(0)
        ]);
    }

    /**
     * Show the item search page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getItemSearch(Request $request)
    { 
        $items = Item::whereIn('id', (array) $request->get('item_ids') ?? [])->released()->get();
        $category = ItemCategory::find($request->get('item_category_id'));

        if($items) {
            // Gather all instances of this item
            $shopItems = UserShopStock::whereIn('item_id', $items->pluck('id')->toArray())
            ->where('stock_type', 'Item')->where('is_visible', 1)->where('quantity', '>', 0)->orderBy('cost', 'ASC')->get();
            $shops = UserShop::whereIn('id', $shopItems->pluck('user_shop_id')->toArray())->orderBy('name', 'ASC')->get()->paginate(20);
        }

         // if there is a category, also get all items in that category
         if ($category) {
            $category_items = Item::where('item_category_id', $category->id)->get();

            if ($shopItems) {
                $shopItems = $shopItems->merge(UserShopStock::whereIn('item_id', $category_items->pluck('id')->toArray())
                ->where('stock_type', 'Item')->where('is_visible', 1)->where('quantity', '>', 0)->orderBy('cost', 'ASC')->get());
            }
            else {
                $shopItems = UserShopStock::whereIn('item_id', $category_items->pluck('id')->toArray())
                ->where('stock_type', 'Item')->where('is_visible', 1)->where('quantity', '>', 0)->orderBy('cost', 'ASC')->get();
            }

            // add category items to items
            $items = $items->merge($category_items);
        }

         // sort shop items by name
         $shopItems = $shopItems->sortBy(function ($item, $key) {
            return $item->item->name;
        });

        return view('home.user_shops.search_items', [
            'searched_items' => count($items) ? $items : null,
            'items'          => Item::released()->orderBy('name')->pluck('name', 'id'),
            'shopItems'      => $items ? $shopItems : null,
            'shops'          => $items ? $shops : null,
            'categories'     => ItemCategory::orderBy('name')->pluck('name', 'id'),
            'category'       => $category,
        ]);
    }

    /**
     * Shows the user's purchase history.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getShopHistory($id)
    {
        $shop = UserShop::find($id);
        if($shop->user_id != Auth::user()->id && !Auth::user()->hasPower('edit_inventories')) abort(404);
        return view('home.user_shops.sale_history', [
            'logs' => $shop->getShopLogs(0),
            'shop' => $shop,
        ]);
    }

    /**
     * transfers item to shop
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\InventoryManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postQuickstockStock(Request $request, UserShopService $service, $id)
    {
        if($service->quickstockStock($request->only(['stock_id','quantity','is_visible','cost','currency_id']), UserShop::where('id', $id)->first(), Auth::user())) {
            flash('Stock edited successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
}
