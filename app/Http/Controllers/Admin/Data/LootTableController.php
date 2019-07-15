<?php

namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;

use Auth;

use App\Models\Item\Item;
use App\Models\Currency\Currency;
use App\Models\Loot\LootTable;

use App\Services\LootService;

use App\Http\Controllers\Controller;

class LootTableController extends Controller
{
    /**
     * Show the loot table index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('admin.loot_tables.loot_tables', [
            'tables' => LootTable::paginate(20)
        ]);
    }
    
    /**
     * Show the create loot table page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateLootTable()
    {
        return view('admin.loot_tables.create_edit_loot_table', [
            'table' => new LootTable,
            'items' => Item::orderBy('name')->pluck('name', 'id'),
            'currencies' => Currency::orderBy('name')->pluck('name', 'id'),
            'tables' => LootTable::orderBy('name')->pluck('name', 'id')
        ]);
    }
    
    /**
     * Show the edit loot table page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditLootTable($id)
    {
        $table = LootTable::find($id);
        if(!$table) abort(404);
        return view('admin.loot_tables.create_edit_loot_table', [
            'table' => $table,
            'items' => Item::orderBy('name')->pluck('name', 'id'),
            'currencies' => Currency::orderBy('name')->pluck('name', 'id'),
            'tables' => LootTable::orderBy('name')->pluck('name', 'id')
        ]);
    }

    public function postCreateEditLootTable(Request $request, LootService $service, $id = null)
    {
        $id ? $request->validate(LootTable::$updateRules) : $request->validate(LootTable::$createRules);
        $data = $request->only([
            'name', 'display_name', 'rewardable_type', 'rewardable_id', 'quantity', 'weight'
        ]);
        if($id && $service->updateLootTable(LootTable::find($id), $data)) {
            flash('Loot table updated successfully.')->success();
        }
        else if (!$id && $table = $service->createLootTable($data)) {
            flash('Loot table created successfully.')->success();
            return redirect()->to('admin/data/loot-tables/edit/'.$table->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /**
     * Get the loot table deletion modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteLootTable($id)
    {
        $table = LootTable::find($id);
        return view('admin.loot_tables._delete_loot_table', [
            'table' => $table,
        ]);
    }

    public function postDeleteLootTable(Request $request, LootService $service, $id)
    {
        if($id && $service->deleteLootTable(LootTable::find($id))) {
            flash('Loot table deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/loot-tables');
    }
    
    /**
     * Get the loot table test roll modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getRollLootTable(Request $request, LootService $service, $id)
    {
        $table = LootTable::find($id);
        if(!$table) abort(404);

        // Normally we'd merge the result tables, but since we're going to be looking at
        // the results of each roll individually on this page, we'll keep them separate
        $results = [];
        for ($i = 0; $i < $request->get('quantity'); $i++)
            $results[] = $table->roll();

        return view('admin.loot_tables._roll_loot_table', [
            'table' => $table,
            'results' => $results,
            'quantity' => $request->get('quantity')
        ]);
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
