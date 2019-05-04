<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Auth;

use App\Models\News;
use App\Services\NewsService;

use App\Http\Controllers\Controller;

class NewsController extends Controller
{
    /**
     * Show the news index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('admin.news.news', [
            'newses' => News::orderBy('post_at', 'DESC')->paginate(20)
        ]);
    }
    
    /**
     * Show the create news page. 
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateNews()
    {
        return view('admin.news.create_edit_news', [
            'news' => new News
        ]);
    }
    
    /**
     * Show the edit news page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditNews($id)
    {
        $news = News::find($id);
        if(!$news) abort(404);
        return view('admin.news.create_edit_news', [
            'news' => $news
        ]);
    }

    public function postCreateEditNews(Request $request, NewsService $service, $id = null)
    {
        $id ? $request->validate(News::$updateRules) : $request->validate(News::$createRules);
        $data = $request->only([
            'title', 'text', 'post_at', 'is_visible'
        ]);
        if($id && $service->updateNews(News::find($id), $data, Auth::user())) {
            flash('News updated successfully.')->success();
        }
        else if (!$id && $news = $service->createNews($data, Auth::user())) {
            flash('News created successfully.')->success();
            return redirect()->to('admin/news/edit/'.$news->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /**
     * Get the news deletion modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteNews($id)
    {
        $news = News::find($id);
        return view('admin.news._delete_news', [
            'news' => $news,
        ]);
    }

    public function postDeleteNews(Request $request, NewsService $service, $id)
    {
        if($id && $service->deleteNews(News::find($id))) {
            flash('News deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/news');
    }

}
