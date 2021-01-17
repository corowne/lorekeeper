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
use App\Services\Stats\ExperienceManager;
use App\Services\Stats\LevelManager;

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

    /*
    *   Character stat level up
    */
    public function postStat($slug, $id, StatManager $service)
    {
        $character = $this->character;
        $stat = CharacterStat::find($id);
        if($service->levelCharaStat($stat, $character, false)) 
        {
            flash('Characters stat levelled successfully!')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
    
    /*
    *  Admin Character stat level up
    */
    public function postAdminStat($slug, $id, StatManager $service)
    {
        $character = $this->character;
        $stat = CharacterStat::find($id);
        if($service->levelCharaStat($stat, $character, true)) 
        {
            flash('Characters stat levelled successfully!')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /*
    *   Character stat current count edit
    */
    public function postEditStat($slug, $id, StatManager $service, Request $request)
    {
        $quantity = $request->get('count');
        $character = $this->character;
        $stat = CharacterStat::find($id);
        if($service->editCharaStat($stat, $character, $quantity)) 
        {
            flash('Characters stat editted successfully!')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Grants exp to characters
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function postExpGrant(Request $request, ExperienceManager $service)
    {
        $character = $this->character;
        if(!$character) abort(404);
        $logType = 'Admin Grant';
        $data = 'Admin Grant of '.$request->get('quantity').' exp';

        if($service->creditExp(null, $character, $logType, $data, $request->get('quantity')))
        {
            flash('Character exp granted successfully!')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Grants stat points to characters
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function postStatGrant(Request $request, StatManager $service)
    {
        $character = $this->character;
        if(!$character) abort(404);
        $logType = 'Admin Grant';
        $data = 'Admin Grant of '.$request->get('quantity').' stat points';

        if($service->creditStat(null, $character, $logType, $data, $request->get('quantity')))
        {
            flash('Character stat point(s) granted successfully!')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    public function postLevel(LevelManager $service)
    {
        $character = $this->character;
        if(!$character) abort(404);

        if($service->characterLevel($character)) 
        {
            flash('Successfully levelled up!')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
}