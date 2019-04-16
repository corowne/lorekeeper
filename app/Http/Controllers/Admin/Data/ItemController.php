<?php

namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;

use Auth;

use App\Models\Item\ItemCategory;
use App\Models\Item\Item;

use App\Services\ItemService;

use App\Http\Controllers\Controller;

class ItemController extends Controller
{
    /**
     * Show the item category index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('admin.items.item_categories', [
            'categories' => ItemCategory::orderBy('sort', 'DESC')->get()
        ]);
    }
    
    /**
     * Show the create item category page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateItemCategory()
    {
        return view('admin.items.create_edit_item_category', [
            'category' => new ItemCategory
        ]);
    }
    
    /**
     * Show the edit item category page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditItemCategory($id)
    {
        $category = ItemCategory::find($id);
        if(!$category) abort(404);
        return view('admin.items.create_edit_item_category', [
            'category' => $category
        ]);
    }

    public function postCreateEditItemCategory(Request $request, ItemService $service, $id = null)
    {
        $id ? $request->validate(ItemCategory::$updateRules) : $request->validate(ItemCategory::$createRules);
        $data = $request->only([
            'name', 'description', 'image', 'remove_image'
        ]);
        if($id && $service->updateItemCategory(ItemCategory::find($id), $data, Auth::user())) {
            flash('Category updated successfully.')->success();
        }
        else if (!$id && $category = $service->createItemCategory($data, Auth::user())) {
            flash('Category created successfully.')->success();
            return redirect()->to('admin/data/item-categories/edit/'.$category->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /**
     * Get the item category deletion modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteItemCategory($id)
    {
        $category = ItemCategory::find($id);
        return view('admin.items._delete_item_category', [
            'category' => $category,
        ]);
    }

    public function postDeleteItemCategory(Request $request, ItemService $service, $id)
    {
        if($id && $service->deleteItemCategory(ItemCategory::find($id))) {
            flash('Category deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/item-categories');
    }

    

    public function postSortItemCategory(Request $request, ItemService $service)
    {
        if($service->sortItemCategory($request->get('sort'))) {
            flash('Category order updated successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }


    /**********************************************************************************************
    
        ITEMS

    **********************************************************************************************/

    /**
     * Show the item category index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getItemIndex(Request $request)
    {
        $query = Item::query();
        $data = $request->only(['item_category_id', 'name']);
        if(isset($data['item_category_id']) && $data['item_category_id'] != 'none') 
            $query->where('item_category_id', $data['item_category_id']);
        if(isset($data['name'])) 
            $query->where('name', 'LIKE', '%'.$data['name'].'%');
        return view('admin.items.items', [
            'items' => $query->paginate(20),
            'categories' => ['none' => 'Any Category'] + ItemCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray()
        ]);
    }
    
    /**
     * Show the create item page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateItem()
    {
        return view('admin.items.create_edit_item', [
            'item' => new Item,
            'categories' => ['none' => 'No category'] + ItemCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray()
        ]);
    }
    
    /**
     * Show the edit item page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditItem($id)
    {
        $item = Item::find($id);
        if(!$item) abort(404);
        return view('admin.items.create_edit_item', [
            'item' => $item,
            'categories' => ['none' => 'No category'] + ItemCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray()
        ]);
    }

    public function postCreateEditItem(Request $request, ItemService $service, $id = null)
    {
        $id ? $request->validate(Item::$updateRules) : $request->validate(Item::$createRules);
        $data = $request->only([
            'name', 'allow_transfer', 'item_category_id', 'description', 'image', 'remove_image'
        ]);
        if($id && $service->updateItem(Item::find($id), $data, Auth::user())) {
            flash('Item updated successfully.')->success();
        }
        else if (!$id && $item = $service->createItem($data, Auth::user())) {
            flash('Item created successfully.')->success();
            return redirect()->to('admin/data/items/edit/'.$item->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /**
     * Get the item deletion modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteItem($id)
    {
        $category = Item::find($id);
        return view('admin.items._delete_item', [
            'item' => $item,
        ]);
    }

    public function postDeleteItem(Request $request, ItemService $service, $id)
    {
        if($id && $service->deleteItem(Item::find($id))) {
            flash('Item deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/items');
    }
}
