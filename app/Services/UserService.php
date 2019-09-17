<?php namespace App\Services;

use App\Services\Service;

use DB;

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
        $user->profile()->create([
            'user_id' => $user->id
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

    public function updatePassword($data, $user)
    {

        DB::beginTransaction();

        try {
            if(!Hash::check($data['old_password'], $user->password)) throw new \Exception("Please enter your old password.");
            if(Hash::make($data['new_password']) == $user->password) throw new \Exception("Please enter a different password.");

            $user->password = Hash::make($data['new_password']);
            $user->save();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    public function updateEmail($data, $user)
    {
        $user->email = $data['email'];
        $user->email_verified_at = null;
        $user->save();

        $user->sendEmailVerificationNotification();

        return true;
    }
    
}