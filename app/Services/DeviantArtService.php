<?php namespace App\Services;

use DB;
use App\Services\Service;
use App\Models\User\UserAlias;
use App\Models\User\UserUpdateLog;
use DeviantPHP\DeviantPHP;

class DeviantArtService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | deviantART Service
    |--------------------------------------------------------------------------
    |
    | Handles connection to deviantART to verify a user's identity.
    |
    */

    /**
     * Setting up for using the deviantART API.
     */
    public function beforeConstruct() {

        $this->deviantart  = null;
        $this->scopes = [
            'user',
        ];

        $this->options = [
            'client_id'      => env('DEVIANTART_CLIENT_ID'),
            'client_secret'  => env('DEVIANTART_CLIENT_SECRET'),
            'redirect_uri'   => url('link'), 

            // Scopes are space-separated
            'scope'         => implode(' ', $this->scopes)
        ];

        $this->deviantart = new DeviantPHP($this->options);

    }
    
    /**
     * Get the Auth URL for dA.
     * 
     * @return string
     */
    public function getAuthURL() {
        return $this->deviantart->createAuthUrl();
    }

    /**
     * Get the access token
     * 
     * @param  string  $code 
     * @param  string  $state 
     */
    public function getAccessToken($code) {

        $token = $this->deviantart->getAccessToken($code); 

        // The token can be saved for continued use, but at the moment it's not needed more than this once.

        return $this->deviantart->getToken();
    }

    /**
     * Link the user's deviantART name to their account
     * 
     * @param  \App\Models\User\User  $user
     */
    public function linkUser($user, $accessToken, $refreshToken) {
        DB::beginTransaction();

        try {
            $this->deviantart->setToken($accessToken, $refreshToken);
            $data = $this->deviantart->getUser();

            if(DB::table('user_aliases')->where('site', 'dA')->where('alias', $data['username'])->exists()) throw new \Exception("Cannot link the same deviantART account multiple times. Please ask a staff member to unlink your old account first.");

            // Save that the user has an alias
            $user->has_alias = 1;
            $user->save();
            // Save the user's alias and set it as the primary alias
            UserAlias::create([
                'user_id' => $user->id,
                'site' => 'dA',
                'alias' => $data['username'],
                'is_visible' => 1,
                'is_primary_alias' => 1,
            ]);
            
            UserUpdateLog::create(['user_id' => $user->id, 'data' => json_encode(['alias' => $data['username']]), 'type' => 'Alias Added']);

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}