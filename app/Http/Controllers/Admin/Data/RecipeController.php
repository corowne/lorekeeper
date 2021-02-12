<?php

namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;

use Auth;

use App\Models\Item\Item;
use App\Models\Item\ItemCategory;
use App\Models\Loot\LootTable;
use App\Models\Raffle\Raffle;
use App\Models\Currency\Currency;
use App\Models\Recipe\Recipe;

use App\Services\RecipeService;

use App\Http\Controllers\Controller;

class RecipeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin / Recipe Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of recipes.
    |
    */

    /**********************************************************************************************
    
        RECIPES

    **********************************************************************************************/

    /**
     * Shows the recipe index.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getRecipeIndex(Request $request)
    {
        $query = Recipe::query();
        $data = $request->only(['name']);
        if(isset($data['name'])) 
            $query->where('name', 'LIKE', '%'.$data['name'].'%');
        return view('admin.recipes.recipes', [
            'recipes' => $query->paginate(20)->appends($request->query())
        ]);
    }
    
    /**
     * Shows the create recipe page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateRecipe()
    {
        return view('admin.recipes.create_edit_recipe', [
            'recipe' => new Recipe,
            'items' => Item::orderBy('name')->pluck('name', 'id'),
            'categories' => ItemCategory::orderBy('name')->pluck('name', 'id'),
            'currencies' => Currency::where('is_user_owned', 1)->orderBy('name')->pluck('name', 'id'),
            'tables' => LootTable::orderBy('name')->pluck('name', 'id'),
            'raffles' => Raffle::where('rolled_at', null)->where('is_active', 1)->orderBy('name')->pluck('name', 'id'),
            'recipes'=> Recipe::orderBy('name')->pluck('name', 'id'),
        ]);
    }
    
    /**
     * Shows the edit recipe page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditRecipe($id)
    {
        $recipe = Recipe::find($id);
        if(!$recipe) abort(404);
        return view('admin.recipes.create_edit_recipe', [
            'recipe' => $recipe,
            'items' => Item::orderBy('name')->pluck('name', 'id'),
            'categories' => ItemCategory::orderBy('name')->pluck('name', 'id'),
            'currencies' => Currency::where('is_user_owned', 1)->orderBy('name')->pluck('name', 'id'),
            'tables' => LootTable::orderBy('name')->pluck('name', 'id'),
            'raffles' => Raffle::where('rolled_at', null)->where('is_active', 1)->orderBy('name')->pluck('name', 'id'),
            'recipes'=> Recipe::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    /**
     * Creates or edits an recipe.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\RecipeService  $service
     * @param  int|null                  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditRecipe(Request $request, RecipeService $service, $id = null)
    {
        $id ? $request->validate(Recipe::$updateRules) : $request->validate(Recipe::$createRules);
        $data = $request->only([
            'name', 'description', 'image', 'remove_image', 'needs_unlocking',
            'ingredient_type', 'ingredient_data', 'ingredient_quantity',
            'rewardable_type', 'rewardable_id', 'reward_quantity', 
            'is_limited', 'limit_type', 'limit_id', 'limit_quantity'
        ]);
        if($id && $service->updateRecipe(Recipe::find($id), $data, Auth::user())) {
            flash('Recipe updated successfully.')->success();
        }
        else if (!$id && $recipe = $service->createRecipe($data, Auth::user())) {
            flash('Recipe created successfully.')->success();
            return redirect()->to('admin/data/recipes/edit/'.$recipe->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /**
     * Gets the recipe deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteRecipe($id)
    {
        $recipe = Recipe::find($id);
        return view('admin.recipes._delete_recipe', [
            'recipe' => $recipe,
        ]);
    }

    /**
     * Creates or edits an recipe.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\RecipeService  $service
     * @param  int                       $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteRecipe(Request $request, RecipeService $service, $id)
    {
        if($id && $service->deleteRecipe(Recipe::find($id))) {
            flash('Recipe deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/recipes');
    }

    /**********************************************************************************************
    
        RECIPE TAGS

    **********************************************************************************************/

    /**
     * Gets the tag addition page.
     *
     * @param  App\Services\RecipeService  $service
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getAddRecipeTag(RecipeService $service, $id)
    {
        $recipe = Recipe::find($id);
        return view('admin.recipes.add_tag', [
            'recipe' => $recipe,
            'tags' => array_diff($service->getRecipeTags(), $recipe->tags()->pluck('tag')->toArray())
        ]);
    }

    /**
     * Adds a tag to an recipe.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\RecipeService  $service
     * @param  int                       $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postAddRecipeTag(Request $request, RecipeService $service, $id)
    {
        $recipe = Recipe::find($id);
        $tag = $request->get('tag');
        if($tag = $service->addRecipeTag($recipe, $tag)) {
            flash('Tag added successfully.')->success();
            return redirect()->to($tag->adminUrl);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Gets the tag editing page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditRecipeTag(RecipeService $service, $id, $tag)
    {
        $recipe = Recipe::find($id);
        $tag = $recipe->tags()->where('tag', $tag)->first();
        if(!$recipe || !$tag) abort(404);
        return view('admin.recipes.edit_tag', [
            'recipe' => $recipe,
            'tag' => $tag
        ] + $tag->getEditData());
    }

    /**
     * Edits tag data for an recipe.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\RecipeService  $service
     * @param  int                       $id
     * @param  string                    $tag
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEditRecipeTag(Request $request, RecipeService $service, $id, $tag)
    {
        $recipe = Recipe::find($id);
        if($service->editRecipeTag($recipe, $tag, $request->all())) {
            flash('Tag edited successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /**
     * Gets the recipe tag deletion modal.
     *
     * @param  int  $id
     * @param  string                    $tag
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteRecipeTag($id, $tag)
    {
        $recipe = Recipe::find($id);
        $tag = $recipe->tags()->where('tag', $tag)->first();
        return view('admin.recipes._delete_recipe_tag', [
            'recipe' => $recipe,
            'tag' => $tag
        ]);
    }

    /**
     * Deletes a tag from an recipe.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\RecipeService  $service
     * @param  int                       $id
     * @param  string                    $tag
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteRecipeTag(Request $request, RecipeService $service, $id, $tag)
    {
        $recipe = Recipe::find($id);
        if($service->deleteRecipeTag($recipe, $tag)) {
            flash('Tag deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/recipes/edit/'.$recipe->id);
    }
}
