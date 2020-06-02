<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\EmbedService;

class EmbedController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Embed Controller
    |--------------------------------------------------------------------------
    |
    | Retrieves the urls for images and thumbnails for displaying on various pages
    |
    */

    /**
     * Return the oEmbed response - full image link and thumbnail link
     *
     * @param  App\Services\EmbedService  $service
     * @param  string  $url
     * @return array
     */
    public function getEmbed(Request $request, EmbedService $service)
    {
        $url = $request->url;
        // Remove any queries
        $url= preg_split('/[?#]/', $url)[0];
        // TODO: Move the patterns elsewhere?
        $accepted_patterns = [
            "/https:\/\/sta.sh\/.*/",
            "/https:\/\/.*.deviantart.com\/art\/.*/",
            "/https:\/\/.*.deviantart.com\/.*\/art\/.*/",
            "/http:\/\/fav.me\/.*/"
        ];
        // Check if its a URL at all
        if(!filter_var($url, FILTER_VALIDATE_URL)) {
            return([
                'error' => "Not an URL"
            ]);
        }
        // Check if its from an accepted domain
        foreach($accepted_patterns as $pattern) {
            if(preg_match($pattern, $url)) {
                $response = $service->getEmbed($url);
                return([
                    'image_url' => $response['url'],
                    'thumbnail_url' => $response['thumbnail_url']
                ]);
            }
        }
        return([
            'error' => "Not an accepted URL"
        ]);        
    }
}
