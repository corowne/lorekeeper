<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Config;
use Illuminate\Http\Request;

use App\Models\Gallery\Gallery;
use App\Models\Gallery\GallerySubmission;
use App\Models\Currency\Currency;

use App\Services\GalleryManager;

use App\Http\Controllers\Controller;

class GalleryController extends Controller
{
    /**
     * Shows the submission index page.
     *
     * @param  string  $status
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSubmissionIndex(Request $request, $status = null)
    {
        $submissions = GallerySubmission::where('status', $status ? ucfirst($status) : 'Pending');
        if($request->get('gallery_id')) 
            $submissions->where(function($query) use ($request) {
                $query->where('gallery_id', $request->get('gallery_id'));
            });
        return view('admin.galleries.submissions_index', [
            'submissions' => $submissions->orderBy('id', 'DESC')->paginate(10)->appends($request->query()),
            'galleries' => ['' => 'Any Gallery'] + Gallery::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray()
        ]);
    }
}
