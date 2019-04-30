<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;

use DB;
use Auth;
use App\Models\User\User;
use App\Models\User\UserCurrency;
use App\Models\Currency\Currency;
use App\Models\Currency\CurrencyLog;
use App\Services\CurrencyManager;

use App\Http\Controllers\Controller;

class BankController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }
    /**
     * Show the user's bank page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('home.bank', [
            'currencyOptions' => Currency::where('allow_user_to_user', 1)->where('is_user_owned', 1)->whereIn('id', UserCurrency::where('user_id', Auth::user()->id)->pluck('currency_id')->toArray())->orderBy('sort_user', 'DESC')->pluck('name', 'id')->toArray(),
            'userOptions' => User::where('id', '!=', Auth::user()->id)->orderBy('name')->pluck('name', 'id')->toArray()

        ]);
    }
    
    public function postTransfer(Request $request, CurrencyManager $service)
    {
        if($service->transferCurrency(Auth::user(), User::find($request->get('user_id')), Currency::where('allow_user_to_user', 1)->where('id', $request->get('currency_id'))->first(), $request->get('quantity'))) {
            flash('Currency transferred successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

}
