<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Carbon\Carbon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\SitePage;

use App\Services\DeviantArtService;
use App\Services\UserService;
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
        elseif($request->get('code')){
            $token = $deviantart->getAccessToken( $request->get('code')); 
            return redirect()->to(url()->current().'?access_token='.$token['access_token'].'&refresh_token='.$token['refresh_token']);
            //header('Location: ?granted='.$servicename);
        }
        // Step 4: verify the token and use the API
        elseif($request->get('access_token') && $request->get('refresh_token')){
            if($deviantart->linkUser(Auth::user(), $request->get('access_token'), $request->get('refresh_token'))) {
                flash('deviantART account has been linked successfully.')->success();
                Auth::user()->updateCharacters();
                Auth::user()->updateArtDesignCredits();
                return redirect()->to('/');
            }
            else {
                foreach($deviantart->errors()->getMessages()['error'] as $error) flash($error)->error();
                return redirect()->back();
            }
        }

        // Step 1: display a login link
        return view('auth.link');
    }

    /**
     * Shows the birthdaying page.
     *
     * @param  \Illuminate\Http\Request        $request
     * @param  App\Services\DeviantArtService  $deviantart
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getBirthday(Request $request)
    {
        // If the user already has a username associated with their account, redirect them
        if(Auth::check() && Auth::user()->birthday) return redirect()->to('/');

        // Step 1: display a login birthday
        return view('auth.birthday');
    }   

    /**
     * Posts the birthdaying page.
     *
     * @param  \Illuminate\Http\Request        $request
     * @param  App\Services\DeviantArtService  $deviantart
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function postBirthday(Request $request)
    {
        $service = new UserService;
        // Make birthday into format we can store
        $data = $request->input('dob');
        $date = $data['day']."-".$data['month']."-".$data['year'];
        $formatDate = Carbon::parse($date);

        if($service->updateBirthday($formatDate, Auth::user())) {
            flash('Birthday added successfully!');
            return redirect()->to('/');
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
            return redirect()->back();
        }
    }   

    /**
     * Shows the birthdaying page.
     *
     * @param  \Illuminate\Http\Request        $request
     * @param  App\Services\DeviantArtService  $deviantart
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getBirthdayBlocked(Request $request)
    {
        // If the user already has a username associated with their account, redirect them
        if(Auth::check() && Auth::user()->checkBirthday) return redirect()->to('/');

        if(Auth::check() && !Auth::user()->birthday) return redirect()->to('birthday');

        // Step 1: display a login birthday
        return view('auth.blocked');
    }   
}
