<?php namespace App\Http\Controllers\Admin;

use Auth;
use DB;
use Request; 
use Exception;

use Carbon\Carbon;
use App\Models\Raffle\RaffleGroup;
use App\Models\Raffle\Raffle;
use App\Models\Raffle\RaffleTicket;

use App\Models\User\User;
use App\Services\RaffleService;
use App\Services\RaffleManager;

use App\Http\Controllers\Controller;

class RaffleController extends Controller
{
    /**************************************************************************
     *  CREATE & EDIT FORMS
     **************************************************************************/
    public function getRaffleIndex()
    {
        $raffles = Raffle::query();
        if (Request::get('is_active')) $raffles->where('is_active', Request::get('is_active'));
        else $raffles->where('is_active', '!=', 2);
        $raffles = $raffles->orderBy('group_id')->orderBy('order');

        return view('admin.raffle.index', [
            'raffles' => $raffles->get(),
            'groups' => RaffleGroup::whereIn('id', $raffles->pluck('group_id')->toArray())->get()->keyBy('id')
        ]);
    }

    public function getCreateEditRaffle($id = null)
    {
        $raffle = null;
        if ($id) {
            $raffle = Raffle::find($id);
            if (!$raffle) abort(404);
        }
        else $raffle = new Raffle;
        return view('admin.raffle._raffle_create_edit', [
            'raffle' => $raffle,
            'groups' => [0 => 'No group'] + RaffleGroup::where('is_active', '<', 2)->pluck('name', 'id')->toArray(),
        ]);
    }

    public function postCreateEditRaffle(RaffleService $service, $id = null)
    {
        $data = Request::only(['name', 'is_active', 'winner_count', 'group_id', 'order']);
        $raffle = null;
        if (!$id) $raffle = $service->createRaffle($data);
        else if ($id) $raffle = $service->updateRaffle($data, Raffle::find($id));
        if ($raffle) {
            flash('Raffle ' . ($id ? 'updated' : 'created') . ' successfully!')->success();
            return redirect()->back();
        }
        else {
            flash('Couldn\'t create raffle.')->error();
            return redirect()->back()->withInput();  
        }
    }
    

    public function getCreateEditRaffleGroup($id = null)
    {
        $group = null;
        if ($id) {
            $group = RaffleGroup::find($id);
            if (!$group) abort(404);
        }
        else $group = new RaffleGroup;
        return view('admin.raffle._group_create_edit', [
            'group' => $group
        ]);
    }

    public function postCreateEditRaffleGroup(RaffleService $service, $id = null)
    {
        $data = Request::only(['name', 'is_active']);
        $group = null;
        if (!$id) $group = $service->createRaffleGroup($data);
        else if ($id) $group = $service->updateRaffleGroup($data, RaffleGroup::find($id));
        if ($group) {
            flash('Raffle group ' . ($id ? 'updated' : 'created') . ' successfully!')->success();
            return redirect()->back();
        }
        else {
            flash('Couldn\'t create raffle group.')->error();
            return redirect()->back()->withInput();  
        }
    }

    public function getRaffleTickets($id)
    {
        $raffle = Raffle::find($id);
        if(!$raffle) abort(404);

        return view('admin.raffle.ticket_index', [
            'raffle' => $raffle,
            'tickets' => $raffle->tickets()->orderBy('id')->paginate(200),
            "page" => Request::get('page') ? Request::get('page') - 1 : 0
        ]);
    }

    public function postCreateRaffleTickets(RaffleManager $service, $id)
    {
        $data = Request::get('names');
        if ($count = $service->addTickets(Raffle::find($id), $data)) {
            flash($count . ' tickets added!')->success();
            return redirect()->back();
        }
        else {
            flash('Couldn\'t add tickets.')->error();
            return redirect()->back()->withInput();  
        }
    }

    public function postDeleteRaffleTicket(RaffleManager $service, $id)
    {
        $data = Request::get('names');
        if ($service->removeTicket(RaffleTicket::find($id))) {
            flash('Ticket removed.')->success();
            return redirect()->back();
        }
        else {
            flash('Couldn\'t remove ticket.')->error();
            return redirect()->back()->withInput();  
        }
    }

    public function getRollRaffle($id)
    {
        $raffle = Raffle::find($id);
        if(!$raffle) abort(404);

        return view('admin.raffle._raffle_roll', [
            'raffle' => $raffle,
        ]);
    }

    public function postRollRaffle(RaffleManager $service, $id)
    {
        if ($service->rollRaffle(Raffle::find($id))) {
            flash('Winners rolled!')->success();
            return redirect()->back();
        }
        else {
            flash('Error in rolling winners.')->error();
            return redirect()->back()->withInput();  
        }
    }

    public function getRollRaffleGroup($id)
    {
        $group = RaffleGroup::find($id);
        if(!$group) abort(404);

        return view('admin.raffle._group_roll', [
            'group' => $group,
        ]);
    }

    public function postRollRaffleGroup(RaffleManager $service, $id)
    {
        if ($service->rollRaffleGroup(Raffle::find($id))) {
            flash('Winners rolled!')->success();
            return redirect()->back();
        }
        else {
            flash('Error in rolling winners.')->error();
            return redirect()->back()->withInput();  
        }
    }

}
