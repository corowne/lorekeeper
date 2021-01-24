<?php

namespace App\Http\Controllers\Users;

use DB;
use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Recipe\Recipe;
use App\Models\User\UserRecipe;
use App\Models\Item\ItemCategory;
use App\Models\Item\Item;
use App\Models\User\User;
use App\Models\User\UserItem;
use App\Models\Currency\Currency;

use App\Services\RecipeService;
use App\Services\RecipeManager;
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
        return view('home.crafting.index', [
            'default' => Recipe::where('needs_unlocking','0')->get(),
        ]);
    }

    /**
     * Shows a recipe's crafting modal.
     *
     * @param  integer  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCraftRecipe(RecipeManager $service, $id)
    {
        $recipe = Recipe::find($id);
        $selected = [];

        if(!$recipe || !Auth::user()) abort(404);

        // foreach ingredient, search for a qualifying item in the users inv, and select items up to the quantity, if insufficient continue onto the next entry
        // until there are no more eligible items, then proceed to the next item
        $selected = $service->pluckIngredients(Auth::user(), $recipe);

        $inventory = UserItem::with('item')->whereNull('deleted_at')->where('count', '>', '0')->where('user_id', Auth::user()->id)->get();

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
    public function postCraftRecipe(Request $request, RecipeManager $service, $id)
    {
        $recipe = Recipe::find($id);
        if(!$recipe) abort(404);

        if($service->craftRecipe($request->only(['stack_id', 'stack_quantity']), $recipe, Auth::user())) {
            flash('Recipe crafted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

}
