<?php namespace App\Http\Controllers\Admin;

use Auth;
use DB;
use Exception;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Raffle\RaffleGroup;
use App\Models\Raffle\Raffle;
use App\Models\Raffle\RaffleTicket;

use App\Models\User\User;
use App\Services\RaffleService;
use App\Services\RaffleManager;

use App\Http\Controllers\Controller;

class RaffleController extends Controller
{
    /**
     * Shows the raffle index.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getRaffleIndex(Request $request)
    {
        $raffles = Raffle::query();
        if ($request->get('is_active')) $raffles->where('is_active', $request->get('is_active'));
        else $raffles->where('is_active', '!=', 2);
        $raffles = $raffles->orderBy('group_id')->orderBy('order');

        return view('admin.raffle.index', [
            'raffles' => $raffles->get(),
            'groups' => RaffleGroup::whereIn('id', $raffles->pluck('group_id')->toArray())->get()->keyBy('id')
        ]);
    }

    /**
     * Shows the create/edit raffle modal.
     *
     * @param  int|null  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
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

    /**
     * Creates or edits a raffle.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  App\Services\RaffleService  $service
     * @param  int|null                    $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditRaffle(Request $request, RaffleService $service, $id = null)
    {
        $data = $request->only(['name', 'is_active', 'winner_count', 'group_id', 'order', 'ticket_cap']);
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

    /**
     * Shows the create/edit raffle group modal.
     *
     * @param  int|null  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
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

    /**
     * Creates or edits a raffle group.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  App\Services\RaffleService  $service
     * @param  int|null                    $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditRaffleGroup(Request $request, RaffleService $service, $id = null)
    {
        $data =$request->only(['name', 'is_active']);
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

    /**
     * Shows the ticket index of a raffle.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int|\\                    $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getRaffleTickets(Request $request, $id)
    {
        $raffle = Raffle::find($id);
        if(!$raffle) abort(404);

        return view('admin.raffle.ticket_index', [
            'raffle' => $raffle,
            'tickets' => $raffle->tickets()->orderBy('id')->paginate(200),
            'users' => User::visible()->orderBy('name')->pluck('name', 'id')->toArray(),
            "page" => $request->get('page') ? $request->get('page') - 1 : 0
        ]);
    }

    /**
     * Creates raffle tickets for a raffle.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  App\Services\RaffleManager  $service
     * @param  int                         $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateRaffleTickets(Request $request, RaffleManager $service, $id)
    {
        $request->validate(RaffleTicket::$createRules);
        $data = $request->only('user_id', 'alias', 'ticket_count');
        if ($count = $service->addTickets(Raffle::find($id), $data)) {
            flash($count . ' tickets added!')->success();
            return redirect()->back();
        }
        else {
            flash('Couldn\'t add tickets.')->error();
            return redirect()->back()->withInput();
        }
    }

    /**
     * Deletes a raffle ticket.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  App\Services\RaffleManager  $service
     * @param  int                         $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteRaffleTicket(Request $request, RaffleManager $service, $id)
    {
        if ($service->removeTicket(RaffleTicket::find($id))) {
            flash('Ticket removed.')->success();
            return redirect()->back();
        }
        else {
            flash('Couldn\'t remove ticket.')->error();
            return redirect()->back()->withInput();
        }
    }

    /**
     * Shows the confirmation modal for rolling a raffle.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getRollRaffle($id)
    {
        $raffle = Raffle::find($id);
        if(!$raffle) abort(404);

        return view('admin.raffle._raffle_roll', [
            'raffle' => $raffle,
        ]);
    }

    /**
     * Rolls a raffle.
     *
     * @param  App\Services\RaffleManager  $service
     * @param  int                         $id
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Shows the confirmation modal for rolling a raffle group.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getRollRaffleGroup($id)
    {
        $group = RaffleGroup::find($id);
        if(!$group) abort(404);

        return view('admin.raffle._group_roll', [
            'group' => $group,
        ]);
    }

    /**
     * Rolls a raffle group.
     *
     * @param  App\Services\RaffleManager  $service
     * @param  int                         $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postRollRaffleGroup(RaffleManager $service, $id)
    {
        if ($service->rollRaffleGroup(RaffleGroup::find($id))) {
            flash('Winners rolled!')->success();
            return redirect()->back();
        }
        else {
            flash('Error in rolling winners.')->error();
            return redirect()->back()->withInput();
        }
    }
}
