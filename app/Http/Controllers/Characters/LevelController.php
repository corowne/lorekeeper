<?php

namespace App\Http\Controllers\Characters;

use Illuminate\Http\Request;

use DB;
use Auth;
use Route;

use App\Models\Character\Character;

use App\Models\Stats\Character\CharaLevels;
use App\Models\Stats\Character\CharacterLevel;
use App\Models\Stats\Character\CharacterStat;
use App\Models\Stats\Character\Stat;

use App\Services\Stats\StatManager;

use App\Http\Controllers\Controller;

class LevelController extends Controller
{
    /* ----------------------------------------
    |
    |   CHARACTER
    |
    |------------------------------------------*/
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $slug = Route::current()->parameter('slug');
            $query = Character::myo(0)->where('slug', $slug);
            if(!(Auth::check() && Auth::user()->hasPower('manage_characters'))) $query->where('is_visible', 1);
            $this->character = $query->first();
            if(!$this->character) abort(404);

            $this->character->updateOwner();
            return $next($request);
        });
    }

    /**
     * Shows the character's level page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex($slug)
    {
        $character = $this->character;
        // create a character level if one doesn't exist
        if(!$character->level)
        {
            $character->level()->create([
                'character_id' => $character->id
            ]);
        }
        //
        $level = $character->level->current_level + 1;
        $next = CharacterLevel::where('level', $level)->first();

        if(!$next) {
            $next = null;
            $width = 100;
        }
        else {
            if($character->level->current_exp < $next->exp_required)
            {
                $width = ($character->level->current_exp / $next->exp_required) * 100;
            }
            else {
                $width = 100;
            }
        }
        return view('character.stats.level_area', [
            'character' => $character,
            'next' => $next,
            'width' => $width,
        ]);
    }

    /*
    *   Character stats
    */
    public function getStatsIndex($slug)
    {
        $character = $this->character;
        $stats = Stat::all();

        // prevents running it when unneeded. if there's an error idk lol
        if($character->stats->count() != $stats->count())
        {
            // we need to do this each time in case a new stat is made. It slows it down but -\(-v-)/-
            foreach($stats as $stat)
            {
                if(!$character->stats->where('stat_id', $stat->id)->first())
                {
                    $character->stats()->create([
                        'character_id' => $character->id,
                        'stat_id' => $stat->id,
                        'count' => $stat->default
                    ]);
                }
            }
        }

        return view('character.stats.stat_area', [
            'character' => $character,
            'stats' => $stats,
        ]);
    }

    public function postStat($slug, $id, StatManager $service)
    {
        $character = $this->character;
        $stat = CharacterStat::find($id);
        if($service->levelCharaStat($stat, $character)) 
        {
            flash('Characters stat levelled successfully!')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
}