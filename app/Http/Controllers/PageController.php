<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\SitePage;
use App\Models\SitePageCategory;

class PageController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Page Controller
    |--------------------------------------------------------------------------
    |
    | Displays site pages, editable from the admin panel.
    |
    */

    /**
     * Shows the page with the given key.
     *
     * @param  string  $key
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getPage($key)
    {
        $page = SitePage::where('key', $key)->where('is_visible', 1)->first();
        if(!$page) abort(404);
        return view('pages.page', ['page' => $page]);
    }

    /**********************************************************************************************
    
        PAGE CATEGORIES

    **********************************************************************************************/
    
    /**
     * Shows the world lore page.
     *
     * @param  string  $key
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getPageSection($key)
    {
        return view('pages.page_sections', [
            'section' => SitePageSection::where('key', $key)->first(),
            'categories' => SitePageCategory::orderBy('sort', 'DESC')->get()
        ]);
    }
}
