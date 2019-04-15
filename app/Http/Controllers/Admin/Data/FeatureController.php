<?php

namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;

use Auth;

use App\Models\Feature\FeatureCategory;

use App\Services\FeatureService;

use App\Http\Controllers\Controller;

class FeatureController extends Controller
{
    /**
     * Show the feature category index.
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
     * Show the create feature category page.
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
     * Show the edit feature category page.
     *
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

    public function postCreateEditFeatureCategory(Request $request, FeatureService $service, $id = null)
    {
        $id ? $request->validate(FeatureCategory::$updateRules) : $request->validate(FeatureCategory::$createRules);
        $data = $request->only([
            'name', 'color', 'description', 'image', 'remove_image'
        ]);
        if($id && $service->updateFeatureCategory(FeatureCategory::find($id), $data, Auth::user())) {
            flash('Category updated successfully.')->success();
        }
        else if (!$id && $category = $service->createFeatureCategory($data, Auth::user())) {
            flash('Category created successfully.')->success();
            return redirect()->to('admin/data/feature-categories/edit/'.$category->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /**
     * Get the feature category deletion modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteFeatureCategory($id)
    {
        $category = FeatureCategory::find($id);
        return view('admin.features._delete_feature_category', [
            'category' => $category,
        ]);
    }

    public function postDeleteFeatureCategory(Request $request, FeatureService $service, $id)
    {
        if($id && $service->deleteFeatureCategory(FeatureCategory::find($id))) {
            flash('Category deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/feature-categories');
    }

    

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
}
