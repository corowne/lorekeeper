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
    /*
    |--------------------------------------------------------------------------
    | Inventory Controller
    |--------------------------------------------------------------------------
    |
    | Handles inventory management for the user.
    |
    */

    /**
     * Shows the user's inventory page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        $categories = ItemCategory::orderBy('sort', 'DESC')->get();
        $items = count($categories) ? 
            Auth::user()->items()
                ->where('count', '>', 0)
                ->orderByRaw('FIELD(item_category_id,'.implode(',', $categories->pluck('id')->toArray()).')')
                ->orderBy('name')
                ->orderBy('updated_at')
                ->get()
                ->groupBy(['item_category_id', 'id']) :
            Auth::user()->items() 
                ->where('count', '>', 0)
                ->orderBy('name')
                ->orderBy('updated_at')
                ->get()
                ->groupBy(['item_category_id', 'id']);
        return view('home.inventory', [
            'categories' => $categories->keyBy('id'),
            'items' => $items,
            'userOptions' => User::visible()->where('id', '!=', Auth::user()->id)->orderBy('name')->pluck('name', 'id')->toArray(),
            'user' => Auth::user()
        ]);
    }

    /**
     * Shows the inventory stack modal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int                       $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getStack(Request $request, $id)
    {
        $first_instance = UserItem::withTrashed()->where('id', $id)->first();
        $readOnly = $request->get('read_only') ? : ((Auth::check() && $first_instance && ($first_instance->user_id == Auth::user()->id || Auth::user()->hasPower('edit_inventories'))) ? 0 : 1);
        $stack = UserItem::where([['user_id', $first_instance->user_id], ['item_id', $first_instance->item_id], ['count', '>', 0]])->get();
        $item = Item::where('id', $first_instance->item_id)->first();

        return view('home._inventory_stack', [
            'stack' => $stack,
            'item' => $item,
            'user' => Auth::user(),
            'userOptions' => ['' => 'Select User'] + User::visible()->where('id', '!=', $first_instance ? $first_instance->user_id : 0)->orderBy('name')->get()->pluck('verified_name', 'id')->toArray(),
            'readOnly' => $readOnly
        ]);
    }

    /**
     * Edits the inventory of involved users.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\InventoryManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEdit(Request $request, InventoryManager $service)
    {
        if(!$request->ids) { flash('No items selected.')->error(); }
        if(!$request->quantities) { flash('Quantities not set.')->error(); }
        
        if($request->ids && $request->quantities) {
            switch($request->action) {
                default:
                    flash('Invalid action selected.')->error();
                    break;
                case 'transfer':
                    return $this->postTransfer($request, $service);
                    break;
                case 'delete':
                    return $this->postDelete($request, $service);
                    break;
                case 'act':
                    return $this->postAct($request);
                    break;
            }
        }
        return redirect()->back();
    }
    
    /**
     * Transfers inventory items to another user.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\InventoryManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    private function postTransfer(Request $request, InventoryManager $service)
    {
        if($service->transferStack(Auth::user(), User::visible()->where('id', $request->get('user_id'))->first(), UserItem::find($request->get('ids')), $request->get('quantities'))) {
            flash('Item transferred successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /**
     * Deletes an inventory stack.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\InventoryManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    private function postDelete(Request $request, InventoryManager $service)
    {
        if($service->deleteStack(Auth::user(), UserItem::find($request->get('ids')), $request->get('quantities'))) {
            flash('Item deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Shows the inventory selection widget.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSelector($id)
    {
        return view('widgets._inventory_select', [
            'user' => Auth::user(),
        ]);
    }
    
    /**
     * Acts on an item based on the item's tag.
     *
     * @param  \Illuminate\Http\Request       $request
     * @return \Illuminate\Http\RedirectResponse
     */
    private function postAct(Request $request)
    {
        $stacks = UserItem::with('item')->find($request->get('ids'));
        $tag = $request->get('tag');
        $service = $stacks->first()->item->hasTag($tag) ? $stacks->first()->item->tag($tag)->service : null;
        if($service && $service->act($stacks, Auth::user(), $request->all())) {
            flash('Item used successfully.')->success();
        }
        else if(!$stacks->first()->item->hasTag($tag)) flash('Invalid action selected.')->error();
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
}
