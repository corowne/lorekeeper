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
}