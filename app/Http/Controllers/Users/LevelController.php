<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;

use DB;
use Auth;
use App\Models\User\User;
use App\Models\User\UserCurrency;
use App\Models\Currency\Currency;
use App\Models\Currency\CurrencyLog;
use App\Services\CurrencyManager;

use App\Models\Stats\User\Level;
use App\Models\Character\Character;

use App\Services\Stats\ExperienceManager;
use App\Services\Stats\StatManager;
use App\Services\Stats\LevelManager;

use App\Http\Controllers\Controller;

class LevelController extends Controller
{
    /* ----------------------------------------
    |
    |   USER
    |
    |------------------------------------------*/

    /**
     * Shows the user's level page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        $user = Auth::user();
        // create a user level if one doesn't exist
        if(!$user->level)
        {
            $user->level()->create([
                'user_id' => $user->id
            ]);
        }
        //
        $level = $user->level->current_level + 1;
        $next = Level::where('level', $level)->first();
        if(!$next) {
            $next = null;
            $width = 100;
        }
        else {
            if($user->level->current_exp < $next->exp_required)
            {
                $width = ($user->level->current_exp / $next->exp_required) * 100;
            }
            else {
                $width = 100;
            }
        }
        return view('home.level', [
            'user' => $user,
            'next' => $next,
            'width' => $width,
            'characters' => $user->characters()->pluck('slug', 'id'),
        ]);
    }

    public function postLevel(LevelManager $service)
    {
        $user = Auth::user();
        if($service->userLevel($user)) 
        {
            flash('Successfully levelled up!')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    public function postTransfer(Request $request, StatManager $service)
    {
        $user = Auth::user();
        $character = Character::find($request->get('id'));

        if($service->userToCharacter($user, $character, $request->get('quantity'))) 
        {
            flash('Successfully transferred stat points!')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
}