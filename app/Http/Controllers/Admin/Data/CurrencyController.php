<?php

namespace App\Http\Controllers\Admin\Data;

use App\Http\Controllers\Controller;
use App\Models\Currency\Currency;
use App\Services\CurrencyService;
use Auth;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin / Currency Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of currencies.
    |
    */

    /**
     * Shows the currency index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('admin.currencies.currencies', [
            'currencies' => Currency::paginate(30),
        ]);
    }

    /**
     * Shows the create currency page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateCurrency()
    {
        return view('admin.currencies.create_edit_currency', [
            'currency' => new Currency,
        ]);
    }

    /**
     * Shows the edit currency page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditCurrency($id)
    {
        $currency = Currency::find($id);
        if (!$currency) {
            abort(404);
        }

        return view('admin.currencies.create_edit_currency', [
            'currency' => $currency,
        ]);
    }

    /**
     * Creates or edits a currency.
     *
     * @param App\Services\CharacterCategoryService $service
     * @param int|null                              $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditCurrency(Request $request, CurrencyService $service, $id = null)
    {
        $id ? $request->validate(Currency::$updateRules) : $request->validate(Currency::$createRules);
        $data = $request->only([
            'is_user_owned', 'is_character_owned',
            'name', 'abbreviation', 'description',
            'is_displayed', 'allow_user_to_user', 'allow_user_to_character', 'allow_character_to_user',
            'icon', 'image', 'remove_icon', 'remove_image',
        ]);
        if ($id && $service->updateCurrency(Currency::find($id), $data, Auth::user())) {
            flash('Currency updated successfully.')->success();
        } elseif (!$id && $currency = $service->createCurrency($data, Auth::user())) {
            flash('Currency created successfully.')->success();

            return redirect()->to('admin/data/currencies/edit/'.$currency->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the currency deletion modal.
     *
     * @param int $id
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

    /**
     * Deletes a currency.
     *
     * @param App\Services\CharacterCategoryService $service
     * @param int                                   $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteCurrency(Request $request, CurrencyService $service, $id)
    {
        if ($id && $service->deleteCurrency(Currency::find($id), Auth::user())) {
            flash('Currency deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/data/currencies');
    }

    /**
     * Shows the sort currency page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSort()
    {
        return view('admin.currencies.sort', [
            'userCurrencies'      => Currency::where('is_user_owned', 1)->orderBy('sort_user', 'DESC')->get(),
            'characterCurrencies' => Currency::where('is_character_owned', 1)->orderBy('sort_character', 'DESC')->get(),
        ]);
    }

    /**
     * Sorts currencies.
     *
     * @param App\Services\CharacterCategoryService $service
     * @param string                                $type
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSortCurrency(Request $request, CurrencyService $service, $type)
    {
        if ($service->sortCurrency($request->get('sort'), $type)) {
            flash('Currency order updated successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }
}
