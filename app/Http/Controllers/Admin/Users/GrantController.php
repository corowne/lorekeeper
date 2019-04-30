<?php

namespace App\Http\Controllers\Admin\Users;

use Auth;
use Config;
use Illuminate\Http\Request;

use App\Models\Item\Item;
use App\Models\Currency\Currency;

use App\Services\CurrencyManager;
use App\Services\InventoryManager;

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
            'userCurrencies' => Currency::where('is_user_owned', 1)->orderBy('sort_user', 'DESC')->pluck('name', 'id')
        ]);
    }

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
            'items' => Item::orderBy('name')->pluck('name', 'id')
        ]);
    }

    public function postItems(Request $request, InventoryManager $service)
    {
        $data = $request->only(['names', 'item_id', 'quantity', 'data', 'disallow_transfer', 'notes']);
        if($service->grantItems($data, Auth::user())) {
            flash('Items granted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

}
