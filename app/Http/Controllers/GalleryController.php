<?php namespace App\Http\Controllers;

use Settings;
use Config;
use Auth;
use View;
use Illuminate\Http\Request;
use App\Models\Gallery\Gallery;
use App\Models\Gallery\GallerySubmission;

use App\Models\User\User;
use App\Models\Character\Character;
use App\Models\Prompt\Prompt;
use App\Models\Currency\Currency;
use App\Models\Comment;

use App\Services\GalleryManager;

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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        View::share('sidebarGalleries', Gallery::whereNull('parent_id')->active()->sort()->get());
    }

    /**
     * Shows the gallery index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getGalleryIndex()
    {
        return view('galleries.index', [
            'galleries' => Gallery::sort()->active()->whereNull('parent_id')->paginate(10),
            'galleryPage' => false,
            'sideGallery' => null
        ]);
    }

    /**
     * Shows a given gallery.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getGallery($id, Request $request)
    {
        $gallery = Gallery::find($id);
        if(!$gallery) abort(404);

        $query = GallerySubmission::where('gallery_id', $gallery->id)->visible(Auth::check() ? Auth::user() : null)->accepted();
        $sort = $request->only(['sort']);

        if($request->get('title')) $query->where(function($query) use ($request) {
            $query->where('gallery_submissions.title', 'LIKE', '%' . $request->get('title') . '%');
        });
        if($request->get('prompt_id')) $query->where('prompt_id', $request->get('prompt_id'));

        if(isset($sort['sort']))
        {
            switch($sort['sort']) {
                case 'alpha':
                    $query->orderBy('title');
                    break;
                case 'alpha-reverse':
                    $query->orderBy('title', 'DESC');
                    break;
                case 'prompt':
                    $query->orderBy('prompt_id', 'DESC');
                    break;
                case 'prompt-reverse':
                    $query->orderBy('prompt_id', 'ASC');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'DESC');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'ASC');
                    break;
            }
        }
        else $query->orderBy('created_at', 'DESC');

        return view('galleries.gallery', [
            'gallery' => $gallery,
            'submissions' => $query->paginate(20)->appends($request->query()),
            'prompts' => [0 => 'Any Prompt'] + Prompt::whereIn('id', GallerySubmission::where('gallery_id', $gallery->id)->visible(Auth::check() ? Auth::user() : null)->accepted()->whereNotNull('prompt_id')->pluck('prompt_id')->toArray())->orderBy('name')->pluck('name', 'id')->toArray(),
            'childSubmissions' => GallerySubmission::whereIn('gallery_id', $gallery->children->pluck('id')->toArray())->where('is_visible', 1)->where('status', 'Accepted'),
            'galleryPage' => true,
            'sideGallery' => $gallery
        ]);
    }

    /**
     * Shows a given submission.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSubmission($id)
    {
        $submission = GallerySubmission::find($id);
        if(!$submission) abort(404);

        if(!$submission->isVisible) {
            if(!Auth::check()) abort(404);
            $isMod = Auth::user()->hasPower('manage_submissions');
            $isOwner = ($submission->user_id == Auth::user()->id);
            $isCollaborator = $submission->collaborators->where('user_id', Auth::user()->id)->first() != null;
            if(!$isMod && (!$isOwner && !$isCollaborator)) abort(404);
        }

        return view('galleries.submission', [
            'submission' => $submission,
            'commentCount' => Comment::where('commentable_type', 'App\Models\Gallery\GallerySubmission')->where('commentable_id', $submission->id)->where('type', 'User-User')->count(),
            'galleryPage' => true,
            'sideGallery' => $submission->gallery
        ]);
    }

    /**
     * Gets the submission favorites list modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSubmissionFavorites($id)
    {
        $submission = GallerySubmission::find($id);
        return view('galleries._submission_favorites', [
            'submission' => $submission,
        ]);
    }

    /**
     * Shows a given submission's detailed queue log.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSubmissionLog($id)
    {
        $submission = GallerySubmission::find($id);
        if(!$submission) abort(404);

        if(!Auth::check()) abort(404);
        $isMod = Auth::user()->hasPower('manage_submissions');
        $isOwner = ($submission->user_id == Auth::user()->id);
        $isCollaborator = $submission->collaborators->where('user_id', Auth::user()->id)->first() != null ? true : false;
        if(!$isMod && !$isOwner && !$isCollaborator) abort(404);

        return view('galleries.submission_log', [
            'submission' => $submission,
            'currency' => Currency::find(Settings::get('group_currency')),
            'galleryPage' => true,
            'sideGallery' => $submission->gallery
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
        $submissions = GallerySubmission::userSubmissions(Auth::user());
        if(!$type) $type = 'Pending';

        $submissions = $submissions->where('status', ucfirst($type));

        return view('galleries.submissions', [
            'submissions' => $submissions->orderBy('id', 'DESC')->paginate(10),
            'galleries' => Gallery::sort()->whereNull('parent_id')->paginate(10),
            'galleryPage' => false,
            'sideGallery' => null
        ]);
    }

    /**
     * Shows the submit page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getNewGallerySubmission(Request $request, $id)
    {
        if(!Auth::check()) abort(404);
        $gallery = Gallery::find($id);
        $closed = !Settings::get('gallery_submissions_open');
        return view('galleries.create_edit_submission', [
            'closed' => $closed,
        ] + ($closed ? [] : [
            'gallery' => $gallery,
            'submission' => new GallerySubmission,
            'prompts' => Prompt::active()->sortAlphabetical()->pluck('name', 'id')->toArray(),
            'users' => User::visible()->orderBy('name')->pluck('name', 'id')->toArray(),
            'currency' => Currency::find(Settings::get('group_currency')),
            'galleryPage' => true,
            'sideGallery' => $gallery
        ]));
    }

    /**
     * Shows the edit submission page.
     *
     * @param  integer  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditGallerySubmission($id)
    {
        if(!Auth::check()) abort(404);
        $submission = GallerySubmission::find($id);
        if(!$submission) abort(404);
        $isMod = Auth::user()->hasPower('manage_submissions');
        $isOwner = ($submission->user_id == Auth::user()->id);
        if(!$isMod && !$isOwner) abort(404);

        // Show inactive prompts in the event of being edited by an admin after acceptance
        $prompts = Auth::user()->hasPower('manage_submissions') && $submission->status == 'Pending' ? Prompt::query() : Prompt::active();

        return view('galleries.create_edit_submission', [
            'closed' => false,
            'gallery' => $submission->gallery,
            'galleryOptions' => Gallery::orderBy('name')->pluck('name', 'id')->toArray(),
            'prompts' => $prompts->sortAlphabetical()->pluck('name', 'id')->toArray(),
            'submission' => $submission,
            'users' => User::visible()->orderBy('name')->pluck('name', 'id')->toArray(),
            'currency' => Currency::find(Settings::get('group_currency')),
            'galleryPage' => true,
            'sideGallery' => $submission->gallery
        ]);
    }

    /**
     * Shows character information.
     *
     * @param  string  $slug
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterInfo($slug)
    {
        $character = Character::visible()->where('slug', $slug)->first();

        return view('galleries._character', [
            'character' => $character,
        ]);
    }

    /**
     * Gets the submission archival modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getArchiveSubmission($id)
    {
        $submission = GallerySubmission::find($id);
        return view('galleries._archive_submission', [
            'submission' => $submission,
        ]);
    }

    /**
     * Creates or edits a new gallery submission.
     *
     * @param  \Illuminate\Http\Request        $request
     * @param  App\Services\GalleryManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditGallerySubmission(Request $request, GalleryManager $service, $id = null)
    {
        $id ? $request->validate(GallerySubmission::$updateRules) : $request->validate(GallerySubmission::$createRules);
        $data = $request->only(['image', 'text', 'title', 'description', 'slug', 'collaborator_id', 'collaborator_data', 'participant_id', 'participant_type', 'gallery_id', 'alert_user', 'prompt_id', 'content_warning']);

        if(!$id && Settings::get('gallery_submissions_reward_currency')) $currencyFormData = $request->only(collect(Config::get('lorekeeper.group_currency_form'))->keys()->toArray());
        else $currencyFormData = null;

        if($id && $service->updateSubmission(GallerySubmission::find($id), $data, Auth::user())) {
            flash('Submission updated successfully.')->success();
        }
        else if (!$id && $gallery = $service->createSubmission($data, $currencyFormData, Auth::user())) {
            flash('Submission created successfully.')->success();
            return redirect()->to('gallery/submissions/pending');
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Archives a submission.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  App\Services\GalleryManager $service
     * @param  int                         $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postArchiveSubmission(Request $request, GalleryManager $service, $id)
    {
        if($id && $service->archiveSubmission(GallerySubmission::find($id), Auth::user())) {
            flash('Submission updated successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Edits/approves collaborator contributions to a submission.
     *
     * @param  \Illuminate\Http\Request        $request
     * @param  App\Services\GalleryManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEditCollaborator(Request $request, GalleryManager $service, $id)
    {
        $data = $request->only(['collaborator_data', 'remove_user']);
        if($service->editCollaborator(GallerySubmission::find($id), $data, Auth::user())) {
            flash('Collaborator info edited successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Favorites/unfavorites a gallery submission.
     *
     * @param  \Illuminate\Http\Request        $request
     * @param  App\Services\GalleryManager  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postFavoriteSubmission(Request $request, GalleryManager $service, $id)
    {
        if($service->favoriteSubmission(GallerySubmission::find($id), Auth::user())) {
            flash('Favorite updated successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

}
