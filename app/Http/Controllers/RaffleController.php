<?php

namespace App\Http\Controllers;

use App\Models\Raffle\Raffle;
use App\Models\Raffle\RaffleGroup;
use Auth;
use Request;

class RaffleController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Raffle Controller
    |--------------------------------------------------------------------------
    |
    | Displays raffles and raffle tickets.
    |
    */

    /**
     * Shows the raffle index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getRaffleIndex()
    {
        $raffles = Raffle::query();
        if (Request::get('view') == 'completed') {
            $raffles->where('is_active', 2);
        } else {
            $raffles->where('is_active', '=', 1);
        }
        $raffles = $raffles->orderBy('group_id')->orderBy('order');

        return view('raffles.index', [
            'raffles' => $raffles->get(),
            'groups'  => RaffleGroup::whereIn('id', $raffles->pluck('group_id')->toArray())->get()->keyBy('id'),
        ]);
    }

    /**
     * Shows tickets for a given raffle.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getRaffleTickets($id)
    {
        $raffle = Raffle::find($id);
        if (!$raffle || !$raffle->is_active) {
            abort(404);
        }
        $userCount = Auth::check() ? $raffle->tickets()->where('user_id', Auth::user()->id)->count() : 0;
        $count = $raffle->tickets()->count();

        return view('raffles.ticket_index', [
            'raffle'    => $raffle,
            'tickets'   => $raffle->tickets()->with('user')->orderBy('id')->paginate(100),
            'count'     => $count,
            'userCount' => $userCount,
            'page'      => Request::get('page') ? Request::get('page') - 1 : 0,
        ]);
    }
}
