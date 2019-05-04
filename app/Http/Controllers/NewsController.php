<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\News;

class NewsController extends Controller
{
    /**
     * Show the news index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        if(Auth::check() && Auth::user()->is_news_unread) Auth::user()->update(['is_news_unread' => 0]);
        return view('news.index', ['newses' => News::visible()->paginate(10)]);
    }
    
    /**
     * Show a news post.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getNews($id, $slug = null)
    {
        $news = News::where('id', $id)->where('is_visible', 1)->first();
        if(!$news) abort(404);
        return view('news.news', ['news' => $news]);
    }
}
