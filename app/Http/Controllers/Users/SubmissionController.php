<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;

use DB;
use Auth;
use Settings;
use App\Models\User\User;
use App\Models\Character\Character;
use App\Models\Item\Item;
use App\Models\Currency\Currency;
use App\Models\Submission\Submission;
use App\Models\Submission\SubmissionCharacter;
use App\Models\Prompt\Prompt;

use App\Services\SubmissionManager;

use App\Http\Controllers\Controller;

class SubmissionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the user's submission log.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex(Request $request)
    {
        $submissions = Submission::where('user_id', Auth::user()->id)->whereNotNull('prompt_id');
        $type = $request->get('type');
        if(!$type) $type = 'Pending';
        
        $submissions = $submissions->where('status', ucfirst($type));

        return view('home.submissions', [
            'submissions' => $submissions->orderBy('id', 'DESC')->paginate(20),
            'isClaims' => false
        ]);
    }
    
    /**
     * Show the submission page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSubmission($id)
    {
        $submission = Submission::viewable(Auth::user())->where('id', $id)->whereNotNull('prompt_id')->first();
        if(!$submission) abort(404);
        return view('home.submission', [
            'submission' => $submission,
            'user' => $submission->user
        ]);
    }

    /**
     * Show the submit page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getNewSubmission(Request $request)
    {
        $closed = !Settings::get('is_prompts_open');
        return view('home.create_submission', [
            'closed' => $closed,
            'isClaim' => false
        ] + ($closed ? [] : [
            'submission' => new Submission,
            'prompts' => Prompt::active()->sortAlphabetical()->pluck('name', 'id')->toArray(),
            'characterCurrencies' => Currency::where('is_character_owned', 1)->orderBy('sort_character', 'DESC')->pluck('name', 'id'),
            'items' => Item::orderBy('name')->pluck('name', 'id'),
            'currencies' => Currency::where('is_user_owned', 1)->orderBy('name')->pluck('name', 'id')
        ]));
    }

    /**
     * Show character information.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterInfo($slug)
    {
        $character = Character::visible()->where('slug', $slug)->first();

        return view('home._character', [
            'character' => $character,
        ]);
    }

    /**
     * Show prompt information.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getPromptInfo($id)
    {
        $prompt = Prompt::active()->where('id', $id)->first();
        if(!$prompt) return response(404);

        return view('home._prompt', [
            'prompt' => $prompt,
            'count' => Submission::where('prompt_id', $id)->where('status', 'Approved')->where('user_id', Auth::user()->id)->count()
        ]);
    }
    
    public function postNewSubmission(Request $request, SubmissionManager $service)
    {
        $request->validate(Submission::$createRules);
        if($service->createSubmission($request->only(['url', 'prompt_id', 'comments', 'slug', 'character_quantity', 'character_currency_id', 'rewardable_type', 'rewardable_id', 'quantity']), Auth::user())) {
            flash('Prompt submitted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('submissions');
    }

    

    /**
     * Show the user's claim log.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getClaimsIndex(Request $request)
    {
        $submissions = Submission::where('user_id', Auth::user()->id)->whereNull('prompt_id');
        $type = $request->get('type');
        if(!$type) $type = 'Pending';
        
        $submissions = $submissions->where('status', ucfirst($type));

        return view('home.submissions', [
            'submissions' => $submissions->orderBy('id', 'DESC')->paginate(20),
            'isClaims' => true
        ]);
    }
    
    /**
     * Show the claim page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getClaim($id)
    {
        $submission = Submission::viewable(Auth::user())->where('id', $id)->whereNull('prompt_id')->first();
        if(!$submission) abort(404);
        return view('home.submission', [
            'submission' => $submission,
            'user' => $submission->user
        ]);
    }
    
    /**
     * Show the submit claim page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getNewClaim(Request $request)
    {
        $closed = !Settings::get('is_claims_open');
        return view('home.create_submission', [
            'closed' => $closed,
            'isClaim' => false
        ] + ($closed ? [] : [
            'submission' => new Submission,
            'characterCurrencies' => Currency::where('is_character_owned', 1)->orderBy('sort_character', 'DESC')->pluck('name', 'id'),
            'items' => Item::orderBy('name')->pluck('name', 'id'),
            'currencies' => Currency::where('is_user_owned', 1)->orderBy('name')->pluck('name', 'id')
        ]));
    }
    
    public function postNewClaim(Request $request, SubmissionManager $service)
    {
        $request->validate(Submission::$createRules);
        if($service->createSubmission($request->only(['url', 'comments', 'slug', 'character_quantity', 'character_currency_id', 'rewardable_type', 'rewardable_id', 'quantity']), Auth::user(), true)) {
            flash('Claim submitted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('claims');
    }

}
