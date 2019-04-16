<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Auth;

use App\Models\SitePage;
use App\Services\PageService;

use App\Http\Controllers\Controller;

class PageController extends Controller
{
    /**
     * Show the page index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('admin.pages.pages', [
            'pages' => SitePage::orderBy('title')->paginate(20)
        ]);
    }
    
    /**
     * Show the create page page. 
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreatePage()
    {
        return view('admin.pages.create_edit_page', [
            'page' => new SitePage
        ]);
    }
    
    /**
     * Show the edit page page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditPage($id)
    {
        $page = SitePage::find($id);
        if(!$page) abort(404);
        return view('admin.pages.create_edit_page', [
            'page' => $page
        ]);
    }

    public function postCreateEditPage(Request $request, PageService $service, $id = null)
    {
        $id ? $request->validate(SitePage::$updateRules) : $request->validate(SitePage::$createRules);
        $data = $request->only([
            'key', 'title', 'text', 'is_visible'
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
     * Get the page deletion modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeletePage($id)
    {
        $page = SitePage::find($id);
        return view('admin.pages._delete_page', [
            'page' => $page,
        ]);
    }

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
}
