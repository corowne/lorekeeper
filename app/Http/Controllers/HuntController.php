<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;

use App\Models\ScavengerHunt\ScavengerHunt;
use App\Models\ScavengerHunt\HuntTarget;
use App\Models\ScavengerHunt\HuntParticipant;
use App\Models\Item\Item;

use App\Services\ScavengerManager;
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
        
        return view('scavenger_hunts.hunt', [
            'hunt' => $hunt,
            'targets' => HuntTarget::where('hunt_id', $id),
            'participant' => HuntParticipant::where('hunt_id', $id)->where('user_id', Auth::user()->id),
        ]);
    }
    
    /**
     * Shows a hunt target.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getTarget($id)
    {
        $target = HuntTarget::find(HuntTarget::where('page_id', $id)->get('id'));
        if(!$target) abort(404);
        
        return view('scavenger_hunts.target', [
            'target' => $target,
            'participant' => HuntParticipant::where('hunt_id', $id)->where('user_id', Auth::user()->id),
        ]);
    }

    /**
     * Claims a hunt target.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\ScavengerManager  $service
     * @param  int|null                       $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postClaimTarget(Request $request, ScavengerManager $service, $id)
    {
        
    }
}
