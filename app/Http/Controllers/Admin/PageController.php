<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Auth;

use App\Models\SitePage;
use App\Models\SitePageCategory;
use App\Models\SitePageSection;
use App\Services\PageService;

use App\Http\Controllers\Controller;

class PageController extends Controller
{
    /**
     * Shows the page index.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex(Request $request)
    {
        $query = SitePage::query();

        $data = $request->only(['page_category_id', 'name']);
        if(isset($data['page_category_id']) && $data['page_category_id'] != 'none') 
            $query->where('page_category_id', $data['page_category_id']);
        if(isset($data['name'])) 
            $query->where('title', 'LIKE', '%'.$data['name'].'%');

        return view('admin.pages.pages', [
            'pages' => $query->orderBy('title')->paginate(20)->appends($request->query()),
            'categories' => [null => 'No category'] + SitePageCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray()
        ]);
    }
    
    /**
     * Shows the create page page. 
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreatePage()
    {
        return view('admin.pages.create_edit_page', [
            'page' => new SitePage,
            'categories' => [null => 'No category'] + SitePageCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray()
        ]);
    }
    
    /**
     * Shows the edit page page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditPage($id)
    {
        $page = SitePage::find($id);
        if(!$page) abort(404);
        return view('admin.pages.create_edit_page', [
            'page' => $page,
            'categories' => [null => 'No category'] + SitePageCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray()
        ]);
    }

    /**
     * Creates or edits a page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\PageService  $service
     * @param  int|null                  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditPage(Request $request, PageService $service, $id = null)
    {
        $id ? $request->validate(SitePage::$updateRules) : $request->validate(SitePage::$createRules);
        $data = $request->only([
            'key', 'title', 'text', 'is_visible', 'can_comment', 'page_category_id'
        ]);
        if($id && $service->updatePage(SitePage::find($id), $data, Auth::user())) {
            flash('Page updated successfully.')->success();
        }
        else if (!$id && $page = $service->createPage($data, Auth::user())) {
            flash('Page created successfully.')->success();
            return redirect()->to('admin/pages/edit/'.$page->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /**
     * Gets the page deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeletePage($id)
    {
        $page = SitePage::find($id);
        return view('admin.pages._delete_page', [
            'page' => $page,
        ]);
    }

    /**
     * Deletes a page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Services\PageService  $service
     * @param  int                       $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeletePage(Request $request, PageService $service, $id)
    {
        if($id && $service->deletePage(SitePage::find($id))) {
            flash('Page deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/pages');
    }

    /**********************************************************************************************
    
        PAGE CATEGORIES

    **********************************************************************************************/

    /**
     * Shows the page category index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCategoryIndex()
    {
        return view('admin.pages.page_categories', [
            'categories' => SitePageCategory::orderBy('sort', 'DESC')->get()
        ]);
    }

    /**
     * Shows the create page category page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreatePageCategory()
    {
        return view('admin.pages.create_edit_page_category', [
            'category' => new SitePageCategory,
            'sections' => [0 => 'None'] + SitePageSection::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray()
        ]);
    }
    
    /**
     * Shows the edit page category page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditPageCategory($id)
    {
        $category = SitePageCategory::find($id);
        if(!$category) abort(404);
        return view('admin.pages.create_edit_page_category', [
            'category' => $category,
            'sections' => [0 => 'None'] + SitePageSection::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray()
        ]);
    }

    /**
     * Creates or edits a page category.
     *
     * @param  \Illuminate\Http\Request     $request
     * @param  App\Services\PageService  $service
     * @param  int|null                     $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditPageCategory(Request $request, PageService $service, $id = null)
    {
        $id ? $request->validate(SitePageCategory::$updateRules) : $request->validate(SitePageCategory::$createRules);
        $data = $request->only([
            'name', 'description', 'image', 'remove_image', 'section_id'
        ]);

        if($id && $service->updatePageCategory(SitePageCategory::find($id), $data)) {
            flash('Category updated successfully.')->success();
        }
        else if (!$id && $category = $service->createPageCategory($data)) {
            flash('Category created successfully.')->success();
            return redirect()->to('admin/page-categories/edit/'.$category->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /**
     * Gets the page category deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeletePageCategory($id)
    {
        $category = SitePageCategory::find($id);
        return view('admin.pages._delete_page_category', [
            'category' => $category,
        ]);
    }

    /**
     * Creates or edits a page category.
     *
     * @param  \Illuminate\Http\Request     $request
     * @param  App\Services\PageService  $service
     * @param  int|null                     $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeletePageCategory(Request $request, PageService $service, $id)
    {
        if($id && $service->deletePageCategory(SitePageCategory::find($id))) {
            flash('Category deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/page-categories');
    }

    /**
     * Sorts page categories.
     *
     * @param  \Illuminate\Http\Request     $request
     * @param  App\Services\PageService  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSortPageCategory(Request $request, PageService $service)
    {
        if($service->sortPageCategory($request->get('sort'))) {
            flash('Category order updated successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
        
    /**********************************************************************************************
    
        PAGE SECTIONS

    **********************************************************************************************/

    /**
     * Shows the page section index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSectionIndex()
    {
        return view('admin.pages.page_sections', [
            'sections' => SitePageSection::orderBy('sort', 'DESC')->get()
        ]);
    }

    /**
     * Shows the create page section page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreatePageSection()
    {
        return view('admin.pages.create_edit_page_section', [
            'section' => new SitePageSection,
            'categories' => SitePageCategory::orderBy('sort', 'DESC')->pluck('name', 'id')
        ]);
    }
    
    /**
     * Shows the edit page section page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditPageSection($id)
    {
        $section = SitePageSection::find($id);
        if(!$section) abort(404);
        return view('admin.pages.create_edit_page_section', [
            'section' => $section,
            'categories' => SitePageCategory::orderBy('sort', 'DESC')->pluck('name', 'id')
        ]);
    }

    /**
     * Creates or edits a page section.
     *
     * @param  \Illuminate\Http\Request     $request
     * @param  App\Services\PageService  $service
     * @param  int|null                     $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditPageSection(Request $request, PageService $service, $id = null)
    {
        $id ? $request->validate(SitePageSection::$updateRules) : $request->validate(SitePageSection::$createRules);
        $data = $request->only([
            'name', 'key'
        ]);
        $contents = $request->only([ 'categories' ]);

        if($id && $service->updatePageSection(SitePageSection::find($id), $data, $contents)) {
            flash('Section updated successfully.')->success();
        }
        else if (!$id && $section = $service->createPageSection($data, $contents)) {
            flash('Section created successfully.')->success();
            return redirect()->to('admin/page-sections/edit/'.$section->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /**
     * Gets the page section deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeletePageSection($id)
    {
        $section = SitePageSection::find($id);
        return view('admin.pages._delete_page_section', [
            'section' => $section,
        ]);
    }

    /**
     * Creates or edits a page category.
     *
     * @param  \Illuminate\Http\Request     $request
     * @param  App\Services\PageService  $service
     * @param  int|null                     $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeletePageSection(Request $request, PageService $service, $id)
    {
        if($id && $service->deletePageSection(SitePageSection::find($id))) {
            flash('Section deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/page-sections');
    }

    /**
     * Sorts page sections.
     *
     * @param  \Illuminate\Http\Request     $request
     * @param  App\Services\PageService  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSortPageSection(Request $request, PageService $service)
    {
        if($service->sortPageSection($request->get('sort'))) {
            flash('Section order updated successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
}
