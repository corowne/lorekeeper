<?php

namespace App\Http\Controllers\Admin\Data;

use App\Http\Controllers\Controller;
use App\Models\Character\CharacterCategory;
use App\Models\Character\Sublist;
use App\Services\CharacterCategoryService;
use Auth;
use Illuminate\Http\Request;

class CharacterCategoryController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin / Character Category Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of character categories.
    |
    */

    /**
     * Shows the character category index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('admin.characters.character_categories', [
            'categories' => CharacterCategory::orderBy('sort', 'DESC')->get(),
        ]);
    }

    /**
     * Shows the create character category page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateCharacterCategory()
    {
        return view('admin.characters.create_edit_character_category', [
            'category' => new CharacterCategory,
            'sublists' => [0 => 'No Sublist'] + Sublist::orderBy('name', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Shows the edit character category page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditCharacterCategory($id)
    {
        $category = CharacterCategory::find($id);
        if (!$category) {
            abort(404);
        }

        return view('admin.characters.create_edit_character_category', [
            'category' => $category,
            'sublists' => [0 => 'No Sublist'] + Sublist::orderBy('name', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Creates or edits a character category.
     *
     * @param App\Services\CharacterCategoryService $service
     * @param int|null                              $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditCharacterCategory(Request $request, CharacterCategoryService $service, $id = null)
    {
        $id ? $request->validate(CharacterCategory::$updateRules) : $request->validate(CharacterCategory::$createRules);
        $data = $request->only([
            'code', 'name', 'description', 'image', 'remove_image', 'masterlist_sub_id',
        ]);
        if ($id && $service->updateCharacterCategory(CharacterCategory::find($id), $data, Auth::user())) {
            flash('Category updated successfully.')->success();
        } elseif (!$id && $category = $service->createCharacterCategory($data, Auth::user())) {
            flash('Category created successfully.')->success();

            return redirect()->to('admin/data/character-categories/edit/'.$category->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the character category deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteCharacterCategory($id)
    {
        $category = CharacterCategory::find($id);

        return view('admin.characters._delete_character_category', [
            'category' => $category,
        ]);
    }

    /**
     * Deletes a character category.
     *
     * @param App\Services\CharacterCategoryService $service
     * @param int                                   $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteCharacterCategory(Request $request, CharacterCategoryService $service, $id)
    {
        if ($id && $service->deleteCharacterCategory(CharacterCategory::find($id), Auth::user())) {
            flash('Category deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/data/character-categories');
    }

    /**
     * Sorts character categories.
     *
     * @param App\Services\CharacterCategoryService $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSortCharacterCategory(Request $request, CharacterCategoryService $service)
    {
        if ($service->sortCharacterCategory($request->get('sort'))) {
            flash('Category order updated successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }
}
