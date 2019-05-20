<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Services\DeviantArtService;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('welcome');
    }

    /**
     * Show the dA account linking page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getLink(DeviantArtService $deviantart, Request $request)
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
