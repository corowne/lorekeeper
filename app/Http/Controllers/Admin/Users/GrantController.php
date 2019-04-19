<?php

namespace App\Http\Controllers\Admin\Users;

use Auth;
use Config;
use Illuminate\Http\Request;

use App\Models\Item\Item;
use App\Models\Currency\Currency;

use App\Services\CurrencyManager;

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
     * Get the rank deletion modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteRank($id)
    {
        $rank = Rank::find($id);
        $editable = Auth::user()->canEditRank($rank);
        if(!$editable) $rank = null;
        return view('admin.users._delete_rank', [
            'rank' => $rank,
            'editable' => $editable
        ]);
    }

    public function postDeleteRank(Request $request, RankService $service, $id)
    {
        if($id && $service->deleteRank(Rank::find($id), Auth::user())) {
            flash('Rank deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    public function postSortRanks(Request $request, RankService $service)
    {
        if($service->sortRanks($request->get('sort'), Auth::user())) {
            flash('Ranks sorted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

}
