<?php namespace App\Http\Controllers;

use Auth;
use Request; 
use App\Models\Gallery\Gallery;
use App\Models\Gallery\GallerySubmission;

class GalleryController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Gallery Controller
    |--------------------------------------------------------------------------
    |
    | Displays galleries and gallery submissions.
    |
    */

    /**
     * Shows the gallery index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getGalleryIndex()
    {
        return view('galleries.index', [
            'galleries' => Gallery::sort()->whereNull('parent_id')->paginate(10),
        ]);
    }

    /**
     * Shows a given gallery.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getGallery($id)
    {
        $gallery = Gallery::find($id);
        if(!$gallery) abort(404);

        return view('galleries.gallery', [
            'gallery' => $gallery,
            'submissions' => GallerySubmission::where('gallery_id', $gallery->id)->orderBy('created_at', 'DESC')->paginate(20),
        ]);
    }
}
