<?php namespace App\Services;

use DB;
use Laravel\Socialite\Facades\Socialite;
use App\Services\Service;
use App\Models\User\UserAlias;
use App\Models\User\UserUpdateLog;

class LinkService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Link Service
    |--------------------------------------------------------------------------
    |
    | Handles connection to social media sites to verify a user's identity.
    |
    */
    
    /**
     * Get the Auth URL for dA.
     *
     * @return string
     */
    public function getAuthRedirect($provider) {
        if($provider == 'deviantart') return Socialite::driver($provider)->setScopes(['user'])->redirect();
        else return Socialite::driver($provider)->redirect();
    }

    /**
     * Link the user's social media account name to their account
     * 
     * @param  \App\Models\User\User  $user
     */
    public function saveProvider($provider, $result, $user) {
        DB::beginTransaction();

        try {
            if(!$result || !$result->nickname) throw new \Exception("Unable to retrieve user data.");

            if(DB::table('user_aliases')->where('site', $provider)->where('alias', $result->nickname)->exists()) throw new \Exception("Cannot link the same account multiple times and/or to different site accounts.");

            // Save the user's alias and set it as the primary alias
            UserAlias::create([
                'user_id' => $user->id,
                'site' => $provider,
                'alias' => $result->nickname,
                'is_visible' => !$user->has_alias,
                'is_primary_alias' => !$user->has_alias,
            ]);

            // Save that the user has an alias
            $user->has_alias = 1;
            $user->save();
            
            UserUpdateLog::create(['user_id' => $user->id, 'data' => json_encode(['alias' => $result->nickname, 'site' => $provider]), 'type' => 'Alias Added']);

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Makes the selected alias the user's primary alias.
     * 
     * @param  \App\Models\User\User  $user
     */
    public function makePrimary($aliasId, $user) {
        DB::beginTransaction();

        try {
            $alias = UserAlias::where('id', $aliasId)->where('user_id', $user->id)->where('is_primary_alias', 0)->first();

            if(!$alias) throw new \Exception("Invalid alias selected.");
            if(!$alias->canMakePrimary) throw new \Exception("This alias cannot be made your primary alias.");

            // Unset the current primary alias
            UserAlias::where('user_id', $user->id)->where('is_primary_alias', 1)->update(['is_primary_alias' => 0]);

            // Update the new primary alias
            $alias->is_visible = 1;
            $alias->is_primary_alias = 1;
            $alias->save();
            
            UserUpdateLog::create(['user_id' => $user->id, 'data' => json_encode(['alias' => $alias->alias, 'site' => $alias->site]), 'type' => 'Primary Alias Changed']);

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Hides or unhides the selected alias.
     * 
     * @param  \App\Models\User\User  $user
     */
    public function hideAlias($aliasId, $user) {
        DB::beginTransaction();

        try {
            $alias = UserAlias::where('id', $aliasId)->where('user_id', $user->id)->where('is_primary_alias', 0)->first();

            if(!$alias) throw new \Exception("Invalid alias selected.");

            // Update the alias's visibility
            $alias->is_visible = !$alias->is_visible;
            $alias->save();
            
            UserUpdateLog::create(['user_id' => $user->id, 'data' => json_encode(['alias' => $alias->alias, 'site' => $alias->site]), 'type' => 'Alias Visibility Changed']);

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Removes the selected alias.
     * 
     * @param  \App\Models\User\User  $user
     */
    public function removeAlias($aliasId, $user) {
        DB::beginTransaction();

        try {
            $alias = UserAlias::where('id', $aliasId)->where('user_id', $user->id)->where('is_primary_alias', 0)->first();

            if(!$alias) throw new \Exception("Invalid alias selected.");
            
            UserUpdateLog::create(['user_id' => $user->id, 'data' => json_encode(['alias' => $alias->alias, 'site' => $alias->site]), 'type' => 'Alias Deleted']);

            // Delete the alias
            $alias->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

}
