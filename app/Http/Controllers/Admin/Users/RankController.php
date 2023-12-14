<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\Rank\Rank;
use App\Services\RankService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RankController extends Controller {
    /**
     * Show the rank index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex() {
        return view('admin.users.ranks', [
            'ranks' => Rank::orderBy('sort', 'DESC')->get(),
        ]);
    }

    /**
     * Get the rank creation modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateRank() {
        return view('admin.users._create_edit_rank', [
            'rank'       => new Rank,
            'rankPowers' => null,
            'powers'     => config('lorekeeper.powers'),
            'editable'   => 1,
        ]);
    }

    /**
     * Get the rank editing modal.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditRank($id) {
        $rank = Rank::find($id);
        $editable = Auth::user()->canEditRank($rank);
        if (!$editable) {
            $rank = null;
        }

        return view('admin.users._create_edit_rank', [
            'rank'       => $rank,
            'rankPowers' => $rank ? $rank->getPowers() : null,
            'powers'     => config('lorekeeper.powers'),
            'editable'   => $editable,
        ]);
    }

    public function postCreateEditRank(Request $request, RankService $service, $id = null) {
        $request->validate(Rank::$rules);
        $data = $request->only(['name', 'description', 'color', 'powers', 'icon']);
        if ($id && $service->updateRank(Rank::find($id), $data, Auth::user())) {
            flash('Rank updated successfully.')->success();
        } elseif ($service->createRank($data, Auth::user())) {
            flash('Rank created successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Get the rank deletion modal.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteRank($id) {
        $rank = Rank::find($id);
        $editable = Auth::user()->canEditRank($rank);
        if (!$editable) {
            $rank = null;
        }

        return view('admin.users._delete_rank', [
            'rank'     => $rank,
            'editable' => $editable,
        ]);
    }

    public function postDeleteRank(Request $request, RankService $service, $id) {
        if ($id && $service->deleteRank(Rank::find($id), Auth::user())) {
            flash('Rank deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    public function postSortRanks(Request $request, RankService $service) {
        if ($service->sortRanks($request->get('sort'), Auth::user())) {
            flash('Ranks sorted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }
}
