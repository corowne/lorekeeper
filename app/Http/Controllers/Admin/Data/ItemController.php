<?php

namespace App\Http\Controllers\Admin\Data;

use App\Http\Controllers\Controller;
use App\Models\Currency\Currency;
use App\Models\Item\Item;
use App\Models\Item\ItemCategory;
use App\Models\Prompt\Prompt;
use App\Models\Shop\Shop;
use App\Models\Shop\ShopStock;
use App\Models\User\User;
use App\Services\ItemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Admin / Item Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of item categories and items.
    |
    */

    /**********************************************************************************************

        ITEM CATEGORIES

    **********************************************************************************************/

    /**
     * Shows the item category index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex() {
        return view('admin.items.item_categories', [
            'categories' => ItemCategory::orderBy('sort', 'DESC')->get(),
        ]);
    }

    /**
     * Shows the create item category page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateItemCategory() {
        return view('admin.items.create_edit_item_category', [
            'category' => new ItemCategory,
        ]);
    }

    /**
     * Shows the edit item category page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditItemCategory($id) {
        $category = ItemCategory::find($id);
        if (!$category) {
            abort(404);
        }

        return view('admin.items.create_edit_item_category', [
            'category' => $category,
        ]);
    }

    /**
     * Creates or edits an item category.
     *
     * @param App\Services\ItemService $service
     * @param int|null                 $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditItemCategory(Request $request, ItemService $service, $id = null) {
        $id ? $request->validate(ItemCategory::$updateRules) : $request->validate(ItemCategory::$createRules);
        $data = $request->only([
            'name', 'description', 'image', 'remove_image', 'is_character_owned', 'character_limit', 'can_name', 'is_visible',
        ]);
        if ($id && $service->updateItemCategory(ItemCategory::find($id), $data, Auth::user())) {
            flash('Category updated successfully.')->success();
        } elseif (!$id && $category = $service->createItemCategory($data, Auth::user())) {
            flash('Category created successfully.')->success();

            return redirect()->to('admin/data/item-categories/edit/'.$category->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the item category deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteItemCategory($id) {
        $category = ItemCategory::find($id);

        return view('admin.items._delete_item_category', [
            'category' => $category,
        ]);
    }

    /**
     * Deletes an item category.
     *
     * @param App\Services\ItemService $service
     * @param int                      $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteItemCategory(Request $request, ItemService $service, $id) {
        if ($id && $service->deleteItemCategory(ItemCategory::find($id), Auth::user())) {
            flash('Category deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/data/item-categories');
    }

    /**
     * Sorts item categories.
     *
     * @param App\Services\ItemService $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSortItemCategory(Request $request, ItemService $service) {
        if ($service->sortItemCategory($request->get('sort'))) {
            flash('Category order updated successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**********************************************************************************************

        ITEMS

    **********************************************************************************************/

    /**
     * Shows the item index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getItemIndex(Request $request) {
        $query = Item::query();
        $data = $request->only(['item_category_id', 'name']);
        if (isset($data['item_category_id']) && $data['item_category_id'] != 'none') {
            $query->where('item_category_id', $data['item_category_id']);
        }
        if (isset($data['name'])) {
            $query->where('name', 'LIKE', '%'.$data['name'].'%');
        }

        return view('admin.items.items', [
            'items'      => $query->paginate(20)->appends($request->query()),
            'categories' => ['none' => 'Any Category'] + ItemCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Shows the create item page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateItem() {
        return view('admin.items.create_edit_item', [
            'item'           => new Item,
            'categories'     => ['none' => 'No category'] + ItemCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'prompts'        => Prompt::where('is_active', 1)->orderBy('id')->pluck('name', 'id'),
            'userCurrencies' => Currency::where('is_user_owned', 1)->orderBy('sort_user', 'DESC')->pluck('name', 'id'),
            'userOptions'    => User::query()->orderBy('name')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Shows the edit item page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditItem($id) {
        $item = Item::find($id);
        if (!$item) {
            abort(404);
        }

        return view('admin.items.create_edit_item', [
            'item'           => $item,
            'categories'     => ['none' => 'No category'] + ItemCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'shops'          => Shop::whereIn('id', ShopStock::where('item_id', $item->id)->pluck('shop_id')->unique()->toArray())->orderBy('sort', 'DESC')->get(),
            'prompts'        => Prompt::where('is_active', 1)->orderBy('id')->pluck('name', 'id'),
            'userCurrencies' => Currency::where('is_user_owned', 1)->orderBy('sort_user', 'DESC')->pluck('name', 'id'),
            'userOptions'    => User::query()->orderBy('name')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Creates or edits an item.
     *
     * @param App\Services\ItemService $service
     * @param int|null                 $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditItem(Request $request, ItemService $service, $id = null) {
        $id ? $request->validate(Item::$updateRules) : $request->validate(Item::$createRules);
        $data = $request->only([
            'name', 'allow_transfer', 'item_category_id', 'description', 'image', 'remove_image', 'rarity',
            'reference_url', 'artist_id', 'artist_url', 'uses', 'shops', 'prompts', 'release', 'currency_id', 'currency_quantity',
            'is_released',
        ]);
        if ($id && $service->updateItem(Item::find($id), $data, Auth::user())) {
            flash('Item updated successfully.')->success();
        } elseif (!$id && $item = $service->createItem($data, Auth::user())) {
            flash('Item created successfully.')->success();

            return redirect()->to('admin/data/items/edit/'.$item->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the item deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteItem($id) {
        $item = Item::find($id);

        return view('admin.items._delete_item', [
            'item' => $item,
        ]);
    }

    /**
     * Creates or edits an item.
     *
     * @param App\Services\ItemService $service
     * @param int                      $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteItem(Request $request, ItemService $service, $id) {
        if ($id && $service->deleteItem(Item::find($id), Auth::user())) {
            flash('Item deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/data/items');
    }

    /**********************************************************************************************

        ITEM TAGS

    **********************************************************************************************/

    /**
     * Gets the tag addition page.
     *
     * @param App\Services\ItemService $service
     * @param int                      $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getAddItemTag(ItemService $service, $id) {
        $item = Item::find($id);

        return view('admin.items.add_tag', [
            'item' => $item,
            'tags' => array_diff($service->getItemTags(), $item->tags()->pluck('tag')->toArray()),
        ]);
    }

    /**
     * Adds a tag to an item.
     *
     * @param App\Services\ItemService $service
     * @param int                      $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postAddItemTag(Request $request, ItemService $service, $id) {
        $item = Item::find($id);
        $tag = $request->get('tag');
        if ($tag = $service->addItemTag($item, $tag, Auth::user())) {
            flash('Tag added successfully.')->success();

            return redirect()->to($tag->adminUrl);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the tag editing page.
     *
     * @param int   $id
     * @param mixed $tag
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditItemTag(ItemService $service, $id, $tag) {
        $item = Item::find($id);
        $tag = $item->tags()->where('tag', $tag)->first();
        if (!$item || !$tag) {
            abort(404);
        }

        return view('admin.items.edit_tag', [
            'item' => $item,
            'tag'  => $tag,
        ] + $tag->getEditData());
    }

    /**
     * Edits tag data for an item.
     *
     * @param App\Services\ItemService $service
     * @param int                      $id
     * @param string                   $tag
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEditItemTag(Request $request, ItemService $service, $id, $tag) {
        $item = Item::find($id);
        if ($service->editItemTag($item, $tag, $request->all(), Auth::user())) {
            flash('Tag edited successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the item tag deletion modal.
     *
     * @param int    $id
     * @param string $tag
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteItemTag($id, $tag) {
        $item = Item::find($id);
        $tag = $item->tags()->where('tag', $tag)->first();

        return view('admin.items._delete_item_tag', [
            'item' => $item,
            'tag'  => $tag,
        ]);
    }

    /**
     * Deletes a tag from an item.
     *
     * @param App\Services\ItemService $service
     * @param int                      $id
     * @param string                   $tag
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteItemTag(Request $request, ItemService $service, $id, $tag) {
        $item = Item::find($id);
        if ($service->deleteItemTag($item, $tag, Auth::user())) {
            flash('Tag deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/data/items/edit/'.$item->id);
    }
}
