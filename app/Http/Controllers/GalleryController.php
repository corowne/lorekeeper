<?php namespace App\Http\Controllers;

use Settings;
use Auth;
use Request; 
use App\Models\Gallery\Gallery;
use App\Models\Gallery\GallerySubmission;

use App\Models\Prompt\Prompt;

use Kris\LaravelFormBuilder\FormBuilder;

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

    /**
     * Shows the user's gallery submission log.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUserSubmissions(Request $request, $type)
    {
        $submissions = GallerySubmission::where('user_id', Auth::user()->id);
        if(!$type) $type = 'Pending';
        
        $submissions = $submissions->where('status', ucfirst($type));

        return view('galleries.submissions', [
            'submissions' => $submissions->orderBy('id', 'DESC')->paginate(20),
            'galleries' => Gallery::sort()->whereNull('parent_id')->paginate(10),
        ]);
    }

    /**
     * Shows the submit page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getNewGallerySubmission(Request $request, $id, FormBuilder $formBuilder)
    {
        $gallery = Gallery::find($id);
        $closed = !Settings::get('gallery_submissions_open');
        return view('galleries.create_edit_submission', [
            'closed' => $closed,
        ] + ($closed ? [] : [
            'gallery' => $gallery,
            'submission' => new GallerySubmission,
            'prompts' => Prompt::active()->sortAlphabetical()->pluck('name', 'id')->toArray(),
            'form' => $formBuilder->create('App\Forms\GroupCurrencyForm'),
        ]));
    }
}
