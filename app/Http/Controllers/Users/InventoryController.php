<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;

use DB;
use Auth;
use App\Models\User\User;
use App\Models\User\UserItem;
use App\Models\Item\Item;
use App\Models\Item\ItemCategory;
use App\Models\Item\UserItemLog;
use App\Services\InventoryManager;

use App\Http\Controllers\Controller;

class InventoryController extends Controller
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
     * Show the user's inventory page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('home.inventory', [
            'categories' => ItemCategory::orderBy('sort', 'DESC')->get()->keyBy('id'),
            'items' => Auth::user()->items()->orderBy('name')->orderBy('updated_at')->get()->groupBy('item_category_id'),
            'userOptions' => User::where('id', '!=', Auth::user()->id)->orderBy('name')->pluck('name', 'id')->toArray(),
            'user' => Auth::user()
        ]);
    }

    public function getStack($id)
    {
        return view('home._inventory_stack', [
            'stack' => $stack = UserItem::where('id', $id)->with('item')->first(),
            'user' => Auth::user(),
            'userOptions' => ['' => 'Select User'] + User::where('id', '!=', $stack ? $stack->user_id : 0)->orderBy('name')->get()->pluck('verified_name', 'id')->toArray(),
        ]);
    }
    
    public function postTransfer(Request $request, InventoryManager $service, $id)
    {
        if($service->transferStack(Auth::user(), User::find($request->get('user_id')), UserItem::where('id', $id)->first())) {
            flash('Item transferred successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    public function postDelete(Request $request, InventoryManager $service, $id)
    {
        if($service->deleteStack(Auth::user(), UserItem::where('id', $id)->first())) {
            flash('Item deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

}
