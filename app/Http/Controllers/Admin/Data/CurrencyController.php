<?php

namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;

use Auth;

use App\Models\Currency\Currency;

use App\Services\CurrencyService;

use App\Http\Controllers\Controller;

class CurrencyController extends Controller
{
    /**
     * Show the currency index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('admin.currencies.currencies', [
            'currencies' => Currency::paginate(30)
        ]);
    }
    
    /**
     * Show the create currency page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateCurrency()
    {
        return view('admin.currencies.create_edit_currency', [
            'currency' => new Currency
        ]);
    }
    
    /**
     * Show the edit currency page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditCurrency($id)
    {
        $currency = Currency::find($id);
        if(!$currency) abort(404);
        return view('admin.currencies.create_edit_currency', [
            'currency' => $currency
        ]);
    }

    public function postCreateEditCurrency(Request $request, CurrencyService $service, $id = null)
    {
        $id ? $request->validate(Currency::$updateRules) : $request->validate(Currency::$createRules);
        $data = $request->only([
            'is_user_owned', 'is_character_owned', 
            'name', 'abbreviation', 'description',
            'is_displayed', 'allow_user_to_user', 'allow_user_to_character', 'allow_character_to_user',
            'icon', 'image'
        ]);
        if($id && $service->updateCurrency(Currency::find($id), $data, Auth::user())) {
            flash('Currency updated successfully.')->success();
        }
        else if (!$id && $currency = $service->createCurrency($data, Auth::user())) {
            flash('Currency created successfully.')->success();
            return redirect()->to('admin/data/currencies/edit/'.$currency->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /**
     * Get the currency deletion modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteCurrency($id)
    {
        $currency = Currency::find($id);
        return view('admin.currencies._delete_currency', [
            'currency' => $currency,
        ]);
    }

    public function postDeleteCurrency(Request $request, CurrencyService $service, $id)
    {
        if($id && $service->deleteCurrency(Currency::find($id))) {
            flash('Currency deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/currencies');
    }

    
    /**
     * Show the sort currency page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSort()
    {
        return view('admin.currencies.sort', [
            'userCurrencies' => Currency::where('is_user_owned', 1)->orderBy('sort_user', 'DESC')->get(),
            'characterCurrencies' => Currency::where('is_character_owned', 1)->orderBy('sort_character', 'DESC')->get()
        ]);
    }

    public function postSortCurrency(Request $request, CurrencyService $service, $type)
    {
        if($service->sortCurrency($request->get('sort'), $type)) {
            flash('Currency order updated successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
}
