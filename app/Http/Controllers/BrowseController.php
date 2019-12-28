<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use Auth;
use Settings;
use App\Models\User\User;
use App\Models\Rank\Rank;

use App\Models\Character\Character;
use App\Models\Species;
use App\Models\Rarity;

class BrowseController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Browse Controller
    |--------------------------------------------------------------------------
    |
    | Displays lists of users and characters.
    |
    */

    /**
     * Shows the user list.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUsers(Request $request)
    {
        $query = User::visible()->join('ranks','users.rank_id', '=', 'ranks.id')->select('ranks.name AS rank_name', 'users.*');
        
        if($request->get('name')) $query->where(function($query) use ($request) {
            $query->where('users.name', 'LIKE', '%' . $request->get('name') . '%')->orWhere('users.alias', 'LIKE', '%' . $request->get('name') . '%');
        });
        if($request->get('rank_id')) $query->where('rank_id', $request->get('rank_id'));

        return view('browse.users', [  
            'users' => $query->orderBy('ranks.sort', 'DESC')->orderBy('name')->paginate(30)->appends($request->query()),
            'ranks' => [0 => 'Any Rank'] + Rank::orderBy('ranks.sort', 'DESC')->pluck('name', 'id')->toArray(),
            'blacklistLink' => Settings::get('blacklist_link')
        ]);
    }

    /**
     * Shows the user blacklist.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getBlacklist(Request $request)
    {
        $canView = false;
        $key = Settings::get('blacklist_key');

        // First, check the display settings for the blacklist...
        $privacy = Settings::get('blacklist_privacy');
        if ( $privacy == 3 ||
            (Auth::check() &&
            ($privacy == 2 ||
            ($privacy == 1 && Auth::user()->isStaff) ||
            ($privacy == 0 && Auth::user()->isAdmin))))
        {
            // Next, check if the blacklist requires a key
            $canView = true;
            if($key != '0' && ($request->get('key') != $key)) $canView = false;

        }
        return view('browse.blacklist', [ 
            'canView' => $canView, 
            'privacy' => $privacy,
            'key' => $key,
            'users' => $canView ? User::where('is_banned', 1)->orderBy('users.name')->paginate(30)->appends($request->query()) : null,
        ]);
    }

    /**
     * Shows the character masterlist.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacters(Request $request)
    {
        $query = Character::with('image')->myo(0);
        
        if($request->get('name')) $query->where(function($query) use ($request) {
            $query->where('characters.name', 'LIKE', '%' . $request->get('name') . '%')->orWhere('characters.slug', 'LIKE', '%' . $request->get('name') . '%');
        });
        // For feature and species searches, we're only searching by the current image of the character.
        if($request->get('feature_id')) {
            $imageIds = DB::table('character_features')->where('feature_id', $request->get('feature_id'))->pluck('character_image_id')->toArray();
            $query->whereIn('character_image_id', array_unique($imageIds));
        }
        if($request->get('species_id')) {
            $imageIds = DB::table('character_images')->where('species_id', $request->get('species_id'))->pluck('id')->toArray();
            $query->whereIn('character_image_id', $imageIds);
        }
        if($request->get('rarity_id')) $query->where('rarity_id', $request->get('rarity_id'));

        return view('browse.masterlist', [  
            'characters' => $query->orderBy('characters.id', 'DESC')->paginate(24)->appends($request->query()),
            'specieses' => [0 => 'Any Species'] + Species::orderBy('specieses.sort', 'DESC')->pluck('name', 'id')->toArray(),
            'rarities' => [0 => 'Any Rarity'] + Rarity::orderBy('rarities.sort', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Shows the MYO slot masterlist.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getMyos(Request $request)
    {
        $query = Character::myo(1);
        
        if($request->get('name')) {
            $users = User::where('users.name', 'LIKE', '%' . $request->get('name') . '%')->orWhere('users.alias', 'LIKE', '%' . $request->get('name') . '%')->pluck('id')->toArray();
            $query->where(function($query) use ($request, $users) {
                $query->where('characters.name', 'LIKE', '%' . $request->get('name') . '%')->orWhere('characters.owner_alias', 'LIKE', '%' . $request->get('name') . '%')->orWhereIn('characters.user_id', $users);
            });
        }
        //if($request->get('species_id')) $query->where('species_id', $request->get('species_id'));
        if($request->get('rarity_id')) $query->where('rarity_id', $request->get('rarity_id'));

        return view('browse.myo_masterlist', [  
            'slots' => $query->orderBy('characters.id', 'DESC')->paginate(30)->appends($request->query()),
            'specieses' => [0 => 'Any Species'] + Species::orderBy('specieses.sort', 'DESC')->pluck('name', 'id')->toArray(),
            'rarities' => [0 => 'Any Rarity'] + Rarity::orderBy('rarities.sort', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }
}
