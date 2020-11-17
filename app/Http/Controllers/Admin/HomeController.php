<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Settings;
use Auth;

use App\Models\Submission\Submission;
use App\Models\Character\CharacterDesignUpdate;
use App\Models\Character\CharacterTransfer;
use App\Models\Trade;
use App\Models\Report\Report;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        $openTransfersQueue = Settings::get('open_transfers_queue');
        return view('admin.index', [
            'submissionCount' => Submission::where('status', 'Pending')->whereNotNull('prompt_id')->count(),
            'claimCount' => Submission::where('status', 'Pending')->whereNull('prompt_id')->count(),
            'designCount' => CharacterDesignUpdate::characters()->where('status', 'Pending')->count(),
            'myoCount' => CharacterDesignUpdate::myos()->where('status', 'Pending')->count(),
            'reportCount' => Report::where('status', 'Pending')->count(),
            'assignedReportCount' => Report::assignedToMe(Auth::user())->count(),
            'openTransfersQueue' => $openTransfersQueue,
            'transferCount' => $openTransfersQueue ? CharacterTransfer::active()->where('is_approved', 0)->count() : 0,
            'tradeCount' => $openTransfersQueue ? Trade::where('status', 'Pending')->count() : 0
        ]);
    }
}
