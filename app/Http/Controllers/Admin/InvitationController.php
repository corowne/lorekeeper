<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Services\InvitationService;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller {
    /**
     * Shows the invitation key index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex() {
        return view('admin.invitations.invitations', [
            'invitations' => Invitation::orderBy('id', 'DESC')->paginate(20),
        ]);
    }

    /**
     * Generates a new invitation key.
     *
     * @param App\Services\InvitationService $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postGenerateKey(InvitationService $service) {
        if ($service->generateInvitation(Auth::user())) {
            flash('Generated invitation successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Generates a new invitation key.
     *
     * @param App\Services\InvitationService $service
     * @param int                            $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteKey(InvitationService $service, $id) {
        $invitation = Invitation::find($id);
        if ($invitation && $service->deleteInvitation($invitation)) {
            flash('Deleted invitation key successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }
}
