<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Currency\Currency;
use App\Models\User\User;
use App\Models\User\UserCurrency;
use App\Services\CurrencyManager;
use Auth;
use Illuminate\Http\Request;

class BankController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Bank Controller
    |--------------------------------------------------------------------------
    |
    | Handles displaying of the user's bank.
    |
    */

    /**
     * Shows the user's bank page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('home.bank', [
            'currencyOptions' => Currency::where('allow_user_to_user', 1)->where('is_user_owned', 1)->whereIn('id', UserCurrency::where('user_id', Auth::user()->id)->pluck('currency_id')->toArray())->orderBy('sort_user', 'DESC')->pluck('name', 'id')->toArray(),
            'userOptions'     => User::visible()->where('id', '!=', Auth::user()->id)->orderBy('name')->pluck('name', 'id')->toArray(),

        ]);
    }

    /**
     * Transfers currency from the user to another.
     *
     * @param App\Services\CurrencyManager $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postTransfer(Request $request, CurrencyManager $service)
    {
        if ($service->transferCurrency(Auth::user(), User::visible()->where('id', $request->get('user_id'))->first(), Currency::where('allow_user_to_user', 1)->where('id', $request->get('currency_id'))->first(), $request->get('quantity'))) {
            flash('Currency transferred successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }
}
