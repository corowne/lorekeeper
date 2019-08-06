<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Auth;
use App\Models\Invitation;
use App\Services\InvitationService;

use App\Http\Controllers\Controller;

class InvitationController extends Controller
{
    /**
     * Show the settings index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('admin.invitations.invitations', [
            'invitations' => Invitation::orderBy('id', 'DESC')->paginate(20)
        ]);
    }

    public function postGenerateKey(InvitationService $service)
    {
        if($service->generateInvitation(Auth::user())) {
            flash('Generated invitation successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    public function postDeleteKey(InvitationService $service, $id)
    {
        $invitation = Invitation::find($id);
        if($invitation && $service->deleteInvitation($invitation)) {
            flash('Deleted invitation key successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

}
