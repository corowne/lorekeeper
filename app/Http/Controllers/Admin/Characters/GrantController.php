<?php

namespace App\Http\Controllers\Admin\Characters;

use Auth;
use Config;
use Illuminate\Http\Request;

use App\Models\Character\Character;
use App\Models\Currency\Currency;
use App\Models\Item\Item;

use App\Services\CurrencyManager;
use App\Services\InventoryManager;

use App\Http\Controllers\Controller;

class GrantController extends Controller
{
    /**
     * Grants or removes currency from a character.
     *
     * @param  string                        $slug
     * @param  \Illuminate\Http\Request      $request
     * @param  App\Services\CurrencyManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCharacterCurrency($slug, Request $request, CurrencyManager $service)
    {
        $data = $request->only(['currency_id', 'quantity', 'data']);
        if($service->grantCharacterCurrencies($data, Character::where('slug', $slug)->first(), Auth::user())) {
            flash('Currency granted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Grants items to characters.
     *
     * @param  string                          $slug
     * @param  \Illuminate\Http\Request        $request
     * @param  App\Services\InventoryManager   $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCharacterItems($slug, Request $request, InventoryManager $service)
    {
        $data = $request->only(['item_ids', 'quantities', 'data', 'disallow_transfer', 'notes']);
        if($service->grantCharacterItems($data,  Character::where('slug', $slug)->first(), Auth::user())) {
            flash('Items granted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
}
