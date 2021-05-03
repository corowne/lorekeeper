<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;

use App\Models\ScavengerHunt\ScavengerHunt;
use App\Models\ScavengerHunt\HuntTarget;
use App\Models\ScavengerHunt\HuntParticipant;
use App\Models\Item\Item;

use App\Services\HuntManager;
use App\Services\InventoryManager;

use App\Http\Controllers\Controller;

class HuntController extends Controller
{

    /**
     * Shows a hunt's information.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getHunt($id)
    {
        $hunt = ScavengerHunt::find($id);
        if(!$hunt) abort(404);
        $participantLog = HuntParticipant::where('hunt_id', $id)->where('user_id', Auth::user()->id)->first();
        if($participantLog) {
            $logArray = [1 => $participantLog['target_1'], 2 => $participantLog['target_2'], 3 => $participantLog['target_3'], 4 => $participantLog['target_4'], 5 => $participantLog['target_5'], 6 => $participantLog['target_6'], 7 => $participantLog['target_7'], 8 => $participantLog['target_8'], 9 => $participantLog['target_9'], 10 => $participantLog['target_10']];
        }
        
        return view('scavenger_hunts.hunt', [
            'hunt' => $hunt,
            'targets' => HuntTarget::where('hunt_id', $id),
            'participantLog' => $participantLog,
            'logArray' => ($participantLog && isset($logArray)) ? $logArray : null,
        ]);
    }
    
    /**
     * Shows a hunt target.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getTarget($pageId)
    {
        $target = HuntTarget::where('page_id', $pageId)->first();
        if(!$target) abort(404);
        $hunt = $target->hunt;
        $participantLog = HuntParticipant::where([
            ['user_id', '=', Auth::user()->id],
            ['hunt_id', '=', $hunt->id],
        ])->first();
        
        return view('scavenger_hunts.target', [
            'target' => $target,
            'hunt' => $hunt,
            'participantLog' => $participantLog,
        ]);
    }

    /**
     * Claims a hunt target.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\HuntManager       $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postClaimTarget(Request $request, HuntManager $service)
    {
        $target = HuntTarget::where('page_id', $request['page_id'])->first();
        if(!$target) abort(404);

        if($service->claimTarget($target, Auth::user())) {
            flash('Successfully claimed prize.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
}
