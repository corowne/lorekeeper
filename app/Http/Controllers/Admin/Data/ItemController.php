<?php

namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;

use Auth;

use App\Models\Item\ItemCategory;

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
            'name', 'color', 'description', 'image', 'remove_image'
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
}
