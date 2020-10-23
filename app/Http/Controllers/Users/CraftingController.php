<?php

namespace App\Http\Controllers\Users;

use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Recipe\Recipe;
use App\Models\Item\ItemCategory;
use App\Models\Item\Item;
use App\Models\User\User;
use App\Models\User\UserItem;
use App\Models\Currency\Currency;

use App\Services\RecipeService;

class CraftingController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Crafting Controller
    |--------------------------------------------------------------------------
    |
    | Handles viewing the user's available and locked recipes, as well as their usage.
    |
    */

    /**
     * Shows the user's trades.
     *
     * @param  string  $type
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex(Request $request)
    {
        $query = Recipe::query();
        $data = $request->only(['name', 'sort']);
        if(isset($data['name'])) $query->where('name', 'LIKE', '%'.$data['name'].'%');
        if(isset($data['sort'])) 
        {
            switch($data['sort']) {
                case 'alpha':
                    $query->sortAlphabetical();
                    break;
                case 'alpha-reverse':
                    $query->sortAlphabetical(true);
                    break;
            }
        }
        else $query->sortAlphabetical();
        return view('home.crafting.index', [
            'recipes' => $query->paginate(20)
        ]);
    }

    /**
     * Shows a recipe's crafting modal.
     *
     * @param  integer  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCraftRecipe($id)
    {
        $recipe = Recipe::find($id);
        
        if(!$recipe || !Auth::user()) abort(404);

        $inventory = UserItem::with('item')->whereNull('deleted_at')->where('count', '>', '0')->where('user_id', Auth::user()->id)
        ->get()
        ->filter(function($userItem){
            return $userItem->isTransferrable == true;
        })
        ->sortBy('item.name');

        // foreach ingredient, search for a qualifying item in the users inv, and select items up to the quantity, if insufficient continue onto the next entry
        // until there are no more eligible items, then proceed to the next item
        $selected = [];
        foreach($recipe->ingredients as $ingredient)
        {
            switch($ingredient->ingredient_type)
            {
                case 'Item':
                    $user_items = $inventory->where('item.id', $ingredient->data[0]);
                    break;
                case 'MultiItem':
                    $user_items = $inventory->whereIn('item.id', $ingredient->data);
                    break;
                case 'Category':
                    $user_items = $inventory->where('item.item_category_id', $ingredient->data[0]);
                    break;
                case 'MultiCategory':
                    $user_items = $inventory->whereIn('item.item_category_id', $ingredient->data);
                    break;
            }
            $quantity_left = $ingredient->quantity;
            while($quantity_left > 0 && count($user_items) > 0)
            {
                $item = $user_items->pop();
                $selected[$item->id] = $item->count >= $quantity_left ? $quantity_left : $item->count;
                $quantity_left -= $selected[$item->id];
            }
        }

        return view('home.crafting._modal_craft', [
            'recipe' => $recipe,
            'categories' => ItemCategory::orderBy('sort', 'DESC')->get(),
            'item_filter' => Item::orderBy('name')->get()->keyBy('id'),
            'inventory' => $inventory,
            'page' => 'craft',
            'selected' => $selected
        ]);
    }

    /**
     * Crafts a recipe
     *
     * @param  integer  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function postCraftRecipe(Request $request, RecipeService $service, $id)
    {
        // TODO: process request
    }
}