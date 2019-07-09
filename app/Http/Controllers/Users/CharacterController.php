<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;

use DB;
use Auth;
use Route;
use Settings;
use App\Models\User\User;
use App\Models\Character\Character;
use App\Models\Currency\Currency;
use App\Models\Currency\CurrencyLog;
use App\Models\User\UserCurrency;
use App\Models\Character\CharacterCurrency;
use App\Models\Character\CharacterTransfer;

use App\Services\CurrencyManager;
use App\Services\CharacterManager;

use App\Http\Controllers\Controller;

class CharacterController extends Controller
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
     * Show the user's characters.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        $characters = Auth::user()->characters()->visible()->get();

        return view('home.characters', [
            'characters' => $characters,
        ]);
    }

    public function postSortCharacters(Request $request, CharacterManager $service)
    {
        if ($service->sortCharacters($request->only(['sort']), Auth::user())) {
            flash('Characters sorted successfully.')->success();
            return redirect()->back();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Show the user's transfers.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getTransfers($type = 'incoming')
    {
        $transfers = CharacterTransfer::query();
        $user = Auth::user();

        switch($type) {
            case 'incoming':
                $transfers->where('recipient_id', $user->id)->active();
                break;
            case 'outgoing':
                $transfers->where('sender_id', $user->id)->active();
                break;
            case 'completed':
                $transfers->where(function($query) use ($user) {
                    $query->where('recipient_id', $user->id)->orWhere('sender_id', $user->id);
                })->completed();
                break;
        }

        return view('home.character_transfers', [
            'transfers' => $transfers->orderBy('id', 'DESC')->paginate(20),
            'transfersQueue' => Settings::get('open_transfers_queue'),
        ]);
    }
    
    public function postHandleTransfer(Request $request, CharacterManager $service, $id)
    {
        if(!Auth::check()) abort(404);

        $action = $request->get('action');
        
        if($action == 'Cancel' && $service->cancelTransfer(['transfer_id' => $id], Auth::user())) {
            flash('Transfer cancelled.')->success();
        }
        else if($service->processTransfer($request->only(['action']) + ['transfer_id' => $id], Auth::user())) {
            flash('Transfer ' . strtolower($action) . 'ed.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

}
