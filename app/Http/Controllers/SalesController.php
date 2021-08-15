<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Sales\Sales;

class SalesController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | sales Controller
    |--------------------------------------------------------------------------
    |
    | Displays sales posts and updates the user's sales read status.
    |
    */

    /**
     * Shows the sales index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        if(Auth::check() && Auth::user()->is_sales_unread) Auth::user()->update(['is_sales_unread' => 0]);
        return view('sales.index', ['saleses' => Sales::visible()->orderBy('id', 'DESC')->paginate(10)]);
    }

    /**
     * Shows a sales post.
     *
     * @param  int          $id
     * @param  string|null  $slug
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSales($id, $slug = null)
    {
        $sales = Sales::where('id', $id)->where('is_visible', 1)->first();
        if(!$sales) abort(404);
        return view('sales.sales', ['sales' => $sales]);
    }
}
