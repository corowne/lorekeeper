<?php

namespace App\Http\Controllers\Admin\Characters;

use Illuminate\Http\Request;

use Auth;

use App\Models\Character\Character;
use App\Models\Character\CharacterCategory;
use App\Models\Rarity;
use App\Models\User\User;
use App\Models\Species;
use App\Models\Feature\Feature;

use App\Services\CharacterManager;

use App\Http\Controllers\Controller;

class CharacterController extends Controller
{
    /**
     * Show the create character page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateCharacter()
    {
        return view('admin.masterlist.create_character', [
            'categories' => CharacterCategory::orderBy('sort')->get(),
            'userOptions' => User::query()->orderBy('name')->pluck('name', 'id')->toArray(),
            'rarities' => ['0' => 'Select Rarity'] + Rarity::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'specieses' => ['0' => 'Select Species'] + Species::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'features' => Feature::orderBy('name')->pluck('name', 'id')->toArray()
        ]);
    }
    
    public function getPullNumber(CharacterManager $service, Request $request)
    {
        return $service->pullNumber($request->get('category'));
    }

    public function postCreateCharacter(Request $request, CharacterManager $service)
    {
        $request->validate(Character::$createRules);
        $data = $request->only([
            'user_id', 'owner_alias', 'character_category_id', 'number', 'slug',
            'description', 'is_visible', 'is_giftable', 'is_tradeable', 'is_sellable',
            'sale_value', 'transferrable_at', 'use_cropper',
            'x0', 'x1', 'y0', 'y1',
            'designer_alias', 'designer_url',
            'artist_alias', 'artist_url',
            'species_id', 'rarity_id', 'feature_id', 'feature_data',
            'image', 'thumbnail', 'image_description'
        ]);
        if ($character = $service->createCharacter($data, Auth::user())) {
            flash('Character created successfully.')->success();
            return redirect()->to($character->url);
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
