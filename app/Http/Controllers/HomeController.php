<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\SitePage;

use App\Services\DeviantArtService;

class HomeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Home Controller
    |--------------------------------------------------------------------------
    |
    | Displays the homepage and page for linking a user's deviantART account.
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
     * Shows the dA account linking page.
     *
     * @param  \Illuminate\Http\Request        $request
     * @param  App\Services\DeviantArtService  $deviantart
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getLink(Request $request, DeviantArtService $deviantart)
    {
        // If the user already has a username associated with their account, redirect them
        if(Auth::check() && Auth::user()->hasAlias) redirect()->to('home');

        // As shown in the token example from chillerlan/php-oauth-deviantart

        // Step 2: redirect to the provider's login screen
        if($request->get('login') === 'DeviantArt'){
            return redirect()->to($deviantart->getAuthURL());
            //header('Location: '.$deviantart->getAuthURL());
        }
        // Step 3: receive the access token
        elseif($request->get('code') && $request->get('state')){
            $deviantart->getAccessToken( $request->get('code'),$request->get('state'));
            return redirect()->to(url()->current().'?granted=DeviantArt');
            //header('Location: ?granted='.$servicename);
        }
        // Step 4: verify the token and use the API
        elseif($request->get('granted') === 'DeviantArt'){
            $deviantart->linkUser(Auth::user());
            flash('deviantART account has been linked successfully.')->success();
            Auth::user()->updateCharacters();
            return redirect()->to('/');
        }

        // Step 1: display a login link
        return view('auth.link');
    }
    
}
