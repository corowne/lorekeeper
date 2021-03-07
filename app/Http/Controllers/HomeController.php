<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Config;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;

use App\Models\SitePage;

use App\Services\LinkService;

class HomeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Home Controller
    |--------------------------------------------------------------------------
    |
    | Displays the homepage and handles redirection for linking a user's social media account.
    |
    */

    /**
     * Shows the homepage.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('welcome', [
            'about' => SitePage::where('key', 'about')->first()
        ]);
    }

    /**
     * Redirects to the appropriate provider.
     *
     * @param  string $provider
     */
    public function getAuthRedirect(LinkService $service, $provider)
    {
        if(!$this->checkProvider($provider, Auth::user())) {
            flash($this->error)->error();
            return redirect()->to(Auth::user()->has_alias ? 'account/aliases' : 'link');
        }

        // Redirect to the provider's authentication page
        return $service->getAuthRedirect($provider);//Socialite::driver($provider)->redirect();
    }

    /**
     * Redirects to the appropriate provider.
     *
     * @param  string $provider
     */
    public function getAuthCallback(LinkService $service, $provider)
    {
        if(!$this->checkProvider($provider, Auth::user())) {
            flash($this->error)->error();
            return redirect()->to(Auth::user()->has_alias ? 'account/aliases' : 'link');
        }

        $result = Socialite::driver($provider)->user();
        if($service->saveProvider($provider, $result, Auth::user())) {
            flash('Account has been linked successfully.')->success();
            Auth::user()->updateCharacters();
            Auth::user()->updateArtDesignCredits();
            return redirect()->to('account/aliases');
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
            return redirect()->to(Auth::user()->has_alias ? 'account/aliases' : 'link');
        }
        return redirect()->to('/');

    }

    private function checkProvider($provider, $user) {
        // Check if the site can be used for authentication
        $isAllowed = false;
        foreach(Config::get('lorekeeper.sites') as $key => $site) {
            if($key == $provider && isset($site['auth'])) {
                // require a primary alias if the user does not already have one
                if(!Auth::user()->has_alias && (!isset($site['primary_alias']) || !$site['primary_alias'])) {
                    $this->error = 'The site you selected cannot be used as your primary alias (means of identification). Please choose a different site to link.';
                    return false;
                }

                $isAllowed = true;
                break;
            }
        }
        if(!$isAllowed) {
            $this->error = 'The site you selected cannot be linked with your account. Please contact an administrator if this is in error!';
            return false;
        }

        // I think there's no harm in linking multiple of the same site as people may want their activity separated into an ARPG account. 
        // Uncomment the following to restrict to one account per site, however.
        // Check if the user already has a username associated with their account
        //if(DB::table('user_aliases')->where('site', $provider)->where('user_id', $user->id)->exists()) {
        //    $this->error = 'You already have a username associated with this website linked to your account.';
        //    return false;
        //}

        return true;
    }
    
}
