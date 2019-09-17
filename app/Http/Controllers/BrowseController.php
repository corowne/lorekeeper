<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use App\Models\User\User;
use App\Models\Rank\Rank;

use App\Models\Character\Character;
use App\Models\Species;
use App\Models\Rarity;

class BrowseController extends Controller
{
    /**
     * Show the user list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getUsers(Request $request)
    {
        $query = User::join('ranks','users.rank_id', '=', 'ranks.id')->select('ranks.name AS rank_name', 'users.*');
        
        if($request->get('name')) $query->where(function($query) use ($request) {
            $query->where('users.name', 'LIKE', '%' . $request->get('name') . '%')->orWhere('users.alias', 'LIKE', '%' . $request->get('name') . '%');
        });
        if($request->get('rank_id')) $query->where('rank_id', $request->get('rank_id'));

        return view('browse.users', [  
            'users' => $query->orderBy('ranks.sort', 'DESC')->orderBy('name')->paginate(30)->appends($request->query()),
            'ranks' => [0 => 'Any Rank'] + Rank::orderBy('ranks.sort', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

    
    /**
     * Show the character masterlist.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacters(Request $request)
    {
        $query = Character::myo(0);
        
        if($request->get('name')) $query->where(function($query) use ($request) {
            $query->where('characters.name', 'LIKE', '%' . $request->get('name') . '%')->orWhere('characters.slug', 'LIKE', '%' . $request->get('name') . '%');
        });
        //if($request->get('species_id')) $query->where('species_id', $request->get('species_id'));
        if($request->get('rarity_id')) $query->where('rarity_id', $request->get('rarity_id'));

        return view('browse.masterlist', [  
            'characters' => $query->orderBy('characters.id', 'DESC')->paginate(24)->appends($request->query()),
            'specieses' => [0 => 'Any Species'] + Species::orderBy('specieses.sort', 'DESC')->pluck('name', 'id')->toArray(),
            'rarities' => [0 => 'Any Rarity'] + Rarity::orderBy('rarities.sort', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

    
    /**
     * Show the MYO slot masterlist.
     *
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
