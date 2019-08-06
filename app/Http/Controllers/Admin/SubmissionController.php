<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Config;
use Illuminate\Http\Request;

use App\Models\Submission\Submission;
use App\Models\Item\Item;
use App\Models\Currency\Currency;
use App\Models\Loot\LootTable;

use App\Services\SubmissionManager;

use App\Http\Controllers\Controller;

class SubmissionController extends Controller
{
    /**
     * Show the submission index page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSubmissionIndex($status)
    {
        return view('admin.submissions.index', [
            'submissions' => Submission::where('status', ucfirst($status))->orderBy('id', 'DESC')->paginate(30)
        ]);
    }
    
    /**
     * Show the submission detail page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSubmission($id)
    {
        return view('admin.submissions.submission', [
            'submission' => $submission = Submission::find($id),
        ] + ($submission->status == 'Pending' ? [
            'characterCurrencies' => Currency::where('is_character_owned', 1)->orderBy('sort_character', 'DESC')->pluck('name', 'id'),
            'items' => Item::orderBy('name')->pluck('name', 'id'),
            'currencies' => Currency::where('is_user_owned', 1)->orderBy('name')->pluck('name', 'id'),
            'tables' => LootTable::orderBy('name')->pluck('name', 'id'),
            'count' => Submission::where('prompt_id', $id)->where('status', 'Approved')->where('user_id', $submission->user_id)->count()
        ] : []));
    }

    public function postSubmission($id, $action, Request $request, SubmissionManager $service)
    {
        $data = $request->only(['slug',  'character_quantity', 'character_currency_id', 'rewardable_type', 'rewardable_id', 'quantity' ]);
        if($action == 'reject' && $service->rejectSubmission($request->only(['staff_comments']) + ['id' => $id], Auth::user())) {
            flash('Submission rejected successfully.')->success();
        }
        elseif($action == 'approve' && $service->approveSubmission($data + ['id' => $id], Auth::user())) {
            flash('Submission approved successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    

}
