<?php

namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;

use Auth;

use App\Models\Feature\FeatureCategory;
use App\Models\Feature\Feature;
use App\Models\Rarity;
use App\Models\Species\Species;
use App\Models\Species\Subtype;

use App\Services\FeatureService;

use App\Http\Controllers\Controller;

class FeatureController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin / Feature Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of character feature categories and features 
    | (AKA traits, which is a reserved keyword in PHP and thus can't be used).
    |
    */

    /**********************************************************************************************
    
        FEATURE CATEGORIES

    **********************************************************************************************/

    /**
     * Shows the feature category index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('admin.features.feature_categories', [
            'categories' => FeatureCategory::orderBy('sort', 'DESC')->get()
        ]);
    }
    
    /**
     * Shows the create feature category page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateFeatureCategory()
    {
        return view('admin.features.create_edit_feature_category', [
            'category' => new FeatureCategory
        ]);
    }
    
    /**
     * Shows the edit feature category page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditFeatureCategory($id)
    {
        $category = FeatureCategory::find($id);
        if(!$category) abort(404);
        return view('admin.features.create_edit_feature_category', [
            'category' => $category
        ]);
    }

    /**
     * Creates or edits a feature category.
     *
     * @param  \Illuminate\Http\Request     $request
     * @param  App\Services\FeatureService  $service
     * @param  int|null                     $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditFeatureCategory(Request $request, FeatureService $service, $id = null)
    {
        $id ? $request->validate(FeatureCategory::$updateRules) : $request->validate(FeatureCategory::$createRules);
        $data = $request->only([
            'name', 'description', 'image', 'remove_image'
        ]);
        if($id && $service->updateFeatureCategory(FeatureCategory::find($id), $data, Auth::user())) {
            flash('Category updated successfully.')->success();
        }
        else if (!$id && $category = $service->createFeatureCategory($data, Auth::user())) {
            flash('Category created successfully.')->success();
            return redirect()->to('admin/data/trait-categories/edit/'.$category->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /**
     * Gets the feature category deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteFeatureCategory($id)
    {
        $category = FeatureCategory::find($id);
        return view('admin.features._delete_feature_category', [
            'category' => $category,
        ]);
    }

    /**
     * Creates or edits a feature category.
     *
     * @param  \Illuminate\Http\Request     $request
     * @param  App\Services\FeatureService  $service
     * @param  int|null                     $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteFeatureCategory(Request $request, FeatureService $service, $id)
    {
        if($id && $service->deleteFeatureCategory(FeatureCategory::find($id), Auth::user())) {
            flash('Category deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/trait-categories');
    }

    /**
     * Sorts feature categories.
     *
     * @param  \Illuminate\Http\Request     $request
     * @param  App\Services\FeatureService  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSortFeatureCategory(Request $request, FeatureService $service)
    {
        if($service->sortFeatureCategory($request->get('sort'))) {
            flash('Category order updated successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**********************************************************************************************
    
        FEATURES

    **********************************************************************************************/

    /**
     * Shows the feature index.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getFeatureIndex(Request $request)
    {
        $query = Feature::query();
        $data = $request->only(['rarity_id', 'feature_category_id', 'species_id', 'name']);
        if(isset($data['rarity_id']) && $data['rarity_id'] != 'none') 
            $query->where('rarity_id', $data['rarity_id']);
        if(isset($data['feature_category_id']) && $data['feature_category_id'] != 'none') 
            $query->where('feature_category_id', $data['feature_category_id']);
        if(isset($data['species_id']) && $data['species_id'] != 'none') 
            $query->where('species_id', $data['species_id']);
        if(isset($data['subtype_id']) && $data['subtype_id'] != 'none') 
            $query->where('subtype_id', $data['subtype_id']);
        if(isset($data['name'])) 
            $query->where('name', 'LIKE', '%'.$data['name'].'%');
        return view('admin.features.features', [
            'features' => $query->paginate(20)->appends($request->query()),
            'rarities' => ['none' => 'Any Rarity'] + Rarity::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'specieses' => ['none' => 'Any Species'] + Species::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'subtypes' => ['none' => 'Any Subtype'] + Subtype::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'categories' => ['none' => 'Any Category'] + FeatureCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray()
        ]);
    }
    
    /**
     * Shows the create feature page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateFeature()
    {
        return view('admin.features.create_edit_feature', [
            'feature' => new Feature,
            'rarities' => ['none' => 'Select a Rarity'] + Rarity::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'specieses' => ['none' => 'No restriction'] + Species::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'subtypes' => ['none' => 'No subtype'] + Subtype::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'categories' => ['none' => 'No category'] + FeatureCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray()
        ]);
    }
    
    /**
     * Shows the edit feature page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditFeature($id)
    {
        $feature = Feature::find($id);
        if(!$feature) abort(404);
        return view('admin.features.create_edit_feature', [
            'feature' => $feature,
            'rarities' => ['none' => 'Select a Rarity'] + Rarity::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'specieses' => ['none' => 'No restriction'] + Species::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'subtypes' => ['none' => 'No subtype'] + Subtype::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'categories' => ['none' => 'No category'] + FeatureCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray()
        ]);
    }

    /**
     * Creates or edits a feature.
     *
     * @param  \Illuminate\Http\Request     $request
     * @param  App\Services\FeatureService  $service
     * @param  int|null                     $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditFeature(Request $request, FeatureService $service, $id = null)
    {
        $id ? $request->validate(Feature::$updateRules) : $request->validate(Feature::$createRules);
        $data = $request->only([
            'name', 'species_id', 'subtype_id', 'rarity_id', 'feature_category_id', 'description', 'image', 'remove_image'
        ]);
        if($id && $service->updateFeature(Feature::find($id), $data, Auth::user())) {
            flash('Trait updated successfully.')->success();
        }
        else if (!$id && $feature = $service->createFeature($data, Auth::user())) {
            flash('Trait created successfully.')->success();
            return redirect()->to('admin/data/traits/edit/'.$feature->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /**
     * Gets the feature deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteFeature($id)
    {
        $feature = Feature::find($id);
        return view('admin.features._delete_feature', [
            'feature' => $feature,
        ]);
    }


    /**
     * Deletes a feature.
     *
     * @param  \Illuminate\Http\Request     $request
     * @param  App\Services\FeatureService  $service
     * @param  int                          $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteFeature(Request $request, FeatureService $service, $id)
    {
        if($id && $service->deleteFeature(Feature::find($id), Auth::user())) {
            flash('Trait deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/traits');
    }
}
