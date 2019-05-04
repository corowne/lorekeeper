<?php

namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;

use Auth;

use App\Models\Character\CharacterCategory;

use App\Services\CharacterCategoryService;

use App\Http\Controllers\Controller;

class CharacterCategoryController extends Controller
{
    /**
     * Show the character category index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('admin.characters.character_categories', [
            'categories' => CharacterCategory::orderBy('sort', 'DESC')->get()
        ]);
    }
    
    /**
     * Show the create character category page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateCharacterCategory()
    {
        return view('admin.characters.create_edit_character_category', [
            'category' => new CharacterCategory
        ]);
    }
    
    /**
     * Show the edit character category page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditCharacterCategory($id)
    {
        $category = CharacterCategory::find($id);
        if(!$category) abort(404);
        return view('admin.characters.create_edit_character_category', [
            'category' => $category
        ]);
    }

    public function postCreateEditCharacterCategory(Request $request, CharacterCategoryService $service, $id = null)
    {
        $id ? $request->validate(CharacterCategory::$updateRules) : $request->validate(CharacterCategory::$createRules);
        $data = $request->only([
            'code', 'name', 'description', 'image', 'remove_image'
        ]);
        if($id && $service->updateCharacterCategory(CharacterCategory::find($id), $data, Auth::user())) {
            flash('Category updated successfully.')->success();
        }
        else if (!$id && $category = $service->createCharacterCategory($data, Auth::user())) {
            flash('Category created successfully.')->success();
            return redirect()->to('admin/data/character-categories/edit/'.$category->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /**
     * Get the character category deletion modal.
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

    public function postDeleteCharacterCategory(Request $request, CharacterCategoryService $service, $id)
    {
        if($id && $service->deleteCharacterCategory(CharacterCategory::find($id))) {
            flash('Category deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/character-categories');
    }

    

    public function postSortCharacterCategory(Request $request, CharacterCategoryService $service)
    {
        if($service->sortCharacterCategory($request->get('sort'))) {
            flash('Category order updated successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
}
