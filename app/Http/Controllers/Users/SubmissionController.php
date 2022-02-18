<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Character\Character;
use App\Models\Currency\Currency;
use App\Models\Item\Item;
use App\Models\Item\ItemCategory;
use App\Models\Prompt\Prompt;
use App\Models\Raffle\Raffle;
use App\Models\Submission\Submission;
use App\Models\User\User;
use App\Models\User\UserItem;
use App\Services\SubmissionManager;
use Auth;
use Config;
use Illuminate\Http\Request;
use Settings;

class SubmissionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Submission Controller
    |--------------------------------------------------------------------------
    |
    | Handles prompt submissions and claims for the user.
    |
    */

    /**********************************************************************************************

        PROMPT SUBMISSIONS

    **********************************************************************************************/

    /**
     * Shows the user's submission log.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex(Request $request)
    {
        $submissions = Submission::with('prompt')->where('user_id', Auth::user()->id)->whereNotNull('prompt_id');
        $type = $request->get('type');
        if (!$type) {
            $type = 'Pending';
        }

        $submissions = $submissions->where('status', ucfirst($type));

        return view('home.submissions', [
            'submissions' => $submissions->orderBy('id', 'DESC')->paginate(20)->appends($request->query()),
            'isClaims'    => false,
        ]);
    }

    /**
     * Shows the submission page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSubmission($id)
    {
        $submission = Submission::viewable(Auth::user())->where('id', $id)->whereNotNull('prompt_id')->first();
        $inventory = isset($submission->data['user']) ? parseAssetData($submission->data['user']) : null;
        if (!$submission) {
            abort(404);
        }

        return view('home.submission', [
            'submission' => $submission,
            'user'       => $submission->user,
            'categories' => ItemCategory::orderBy('sort', 'DESC')->get(),
            'inventory'  => $inventory,
            'itemsrow'   => Item::all()->keyBy('id'),
        ]);
    }

    /**
     * Shows the submit page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getNewSubmission(Request $request)
    {
        $closed = !Settings::get('is_prompts_open');
        $inventory = UserItem::with('item')->whereNull('deleted_at')->where('count', '>', '0')->where('user_id', Auth::user()->id)->get();

        return view('home.create_submission', [
            'closed'  => $closed,
            'isClaim' => false,
        ] + ($closed ? [] : [
            'submission'          => new Submission,
            'prompts'             => Prompt::active()->sortAlphabetical()->pluck('name', 'id')->toArray(),
            'characterCurrencies' => Currency::where('is_character_owned', 1)->orderBy('sort_character', 'DESC')->pluck('name', 'id'),
            'categories'          => ItemCategory::orderBy('sort', 'DESC')->get(),
            'item_filter'         => Item::orderBy('name')->released()->get()->keyBy('id'),
            'items'               => Item::orderBy('name')->released()->pluck('name', 'id'),
            'character_items'     => Item::whereIn('item_category_id', ItemCategory::where('is_character_owned', 1)->pluck('id')->toArray())->orderBy('name')->released()->pluck('name', 'id'),
            'currencies'          => Currency::where('is_user_owned', 1)->orderBy('name')->pluck('name', 'id'),
            'inventory'           => $inventory,
            'page'                => 'submission',
            'expanded_rewards'    => Config::get('lorekeeper.extensions.character_reward_expansion.expanded'),
        ]));
    }

    /**
     * Shows character information.
     *
     * @param string $slug
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
     * Shows prompt information.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getPromptInfo($id)
    {
        $prompt = Prompt::active()->where('id', $id)->first();
        if (!$prompt) {
            return response(404);
        }

        return view('home._prompt', [
            'prompt' => $prompt,
            'count'  => Submission::where('prompt_id', $id)->where('status', 'Approved')->where('user_id', Auth::user()->id)->count(),
        ]);
    }

    /**
     * Creates a new submission.
     *
     * @param App\Services\SubmissionManager $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postNewSubmission(Request $request, SubmissionManager $service)
    {
        $request->validate(Submission::$createRules);
        if ($service->createSubmission($request->only(['url', 'prompt_id', 'comments', 'slug', 'character_rewardable_type', 'character_rewardable_id', 'character_rewardable_quantity', 'rewardable_type', 'rewardable_id', 'quantity', 'stack_id', 'stack_quantity', 'currency_id', 'currency_quantity']), Auth::user())) {
            flash('Prompt submitted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }

            return redirect()->back();
        }

        return redirect()->to('submissions');
    }

    /**********************************************************************************************

        CLAIMS

    **********************************************************************************************/

    /**
     * Shows the user's claim log.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getClaimsIndex(Request $request)
    {
        $submissions = Submission::where('user_id', Auth::user()->id)->whereNull('prompt_id');
        $type = $request->get('type');
        if (!$type) {
            $type = 'Pending';
        }

        $submissions = $submissions->where('status', ucfirst($type));

        return view('home.submissions', [
            'submissions' => $submissions->orderBy('id', 'DESC')->paginate(20)->appends($request->query()),
            'isClaims'    => true,
        ]);
    }

    /**
     * Shows the claim page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getClaim($id)
    {
        $submission = Submission::viewable(Auth::user())->where('id', $id)->whereNull('prompt_id')->first();
        $inventory = isset($submission->data['user']) ? parseAssetData($submission->data['user']) : null;
        if (!$submission) {
            abort(404);
        }

        return view('home.submission', [
            'submission' => $submission,
            'user'       => $submission->user,
            'categories' => ItemCategory::orderBy('sort', 'DESC')->get(),
            'itemsrow'   => Item::all()->keyBy('id'),
            'inventory'  => $inventory,
        ]);
    }

    /**
     * Shows the submit claim page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getNewClaim(Request $request)
    {
        $closed = !Settings::get('is_claims_open');
        $inventory = UserItem::with('item')->whereNull('deleted_at')->where('count', '>', '0')->where('user_id', Auth::user()->id)->get();

        return view('home.create_submission', [
            'closed'  => $closed,
            'isClaim' => true,
        ] + ($closed ? [] : [
            'submission'          => new Submission,
            'characterCurrencies' => Currency::where('is_character_owned', 1)->orderBy('sort_character', 'DESC')->pluck('name', 'id'),
            'categories'          => ItemCategory::orderBy('sort', 'DESC')->get(),
            'inventory'           => $inventory,
            'item_filter'         => Item::orderBy('name')->released()->get()->keyBy('id'),
            'items'               => Item::orderBy('name')->released()->pluck('name', 'id'),
            'currencies'          => Currency::where('is_user_owned', 1)->orderBy('name')->pluck('name', 'id'),
            'raffles'             => Raffle::where('rolled_at', null)->where('is_active', 1)->orderBy('name')->pluck('name', 'id'),
            'page'                => 'submission',
            'expanded_rewards'    => Config::get('lorekeeper.extensions.character_reward_expansion.expanded'),
        ]));
    }

    /**
     * Creates a new claim.
     *
     * @param App\Services\SubmissionManager $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postNewClaim(Request $request, SubmissionManager $service)
    {
        $request->validate(Submission::$createRules);
        if ($service->createSubmission($request->only(['url', 'comments', 'stack_id', 'stack_quantity', 'slug', 'character_rewardable_type', 'character_rewardable_id', 'character_rewardable_quantity', 'rewardable_type', 'rewardable_id', 'quantity', 'currency_id', 'currency_quantity']), Auth::user(), true)) {
            flash('Claim submitted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }

            return redirect()->back();
        }

        return redirect()->to('claims');
    }
}
