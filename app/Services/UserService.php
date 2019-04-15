<?php namespace App\Services;

use App\Services\Service;

use App\Models\User\User;
use App\Models\Rank\Rank;
use Illuminate\Support\Facades\Hash;

class UserService extends Service
{

    public function createUser($data)
    {
        // If the rank is not given, create a user with the lowest existing rank.
        if(!isset($data['rank_id'])) $data['rank_id'] = Rank::orderBy('sort')->first()->id;

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'rank_id' => $data['rank_id'],
            'password' => Hash::make($data['password']),
        ]);
        $user->settings()->create([
            'user_id' => $user->id,
        ]);

        return $user;
    }
    
    public function updateUser($data)
    {
        $user = User::find($data['id']);
        if(isset($data['password'])) $data['password'] = Hash::make($data['password']);
        if($user) $user->update($data);

        return $user;
    }
    
}