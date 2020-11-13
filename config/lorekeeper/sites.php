<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sites
    |--------------------------------------------------------------------------
    |
    | This is a list of sites and appropriate regex for use in url matching,
    | for formatting links or for performing auth operations.
    | Feel free to add more sites to the "General" section, it is used solely 
    | for link formatting, but be careful about making changes to any sites in the
    | "Auth" section as they are used for site functions.
    |
    */

    /**********************************************************************************************
     
        AUTH

    **********************************************************************************************/

    // Sites in this section use two additional keys; dA is used here as an example to demonstrate them
    // 1 = true/0 = false for both of them.
    'dA' => [
        'fullName' => 'deviantArt',
        'regex' => '/deviantart\.com\/([A-Za-z0-9_-]+)/',
        'link' => 'deviantart.com',
        // Auth is for whether or not the site should offer this provider as an option for users
        // to authenticate with,
        'auth' => 1,
        // while primary alias is whether or not an alias on this site can be a user's primary alias.
        'primary_alias' => 1
    ],

    /**********************************************************************************************
     
        GENERAL

    **********************************************************************************************/

    'twitter' => [
        'fullName' => 'Twitter',
        'regex' => '/twitter\.com\/([A-Za-z0-9_-]+)/',
        'link' => 'twitter.com'
    ],
    'ig' => [
        'fullName' => 'Instagram',
        'regex' => '/instagram\.com\/([A-Za-z0-9_-]+)/',
        'link' => 'instagram.com'
    ],
    'tumblr' => [
        'fullName' => 'Tumblr',
        'regex' => '/([A-Za-z0-9_-]+)\.tumblr\.com/',
        'link' => 'tumblr.com'
    ],
    'TH' => [
        'fullName' => 'Toyhou.se',
        'regex' => '/toyhou\.se\/([A-Za-z0-9_-]+)/',
        'link' => 'toyhou.se'
    ],
    'artstation' => [
        'fullName' => 'Artstation',
        'regex' => '/artstation\.com\/([A-Za-z0-9_-]+)/',
        'link' => 'artstation.com'
    ],
    'picarto' => [
        'fullName' => 'Picarto',
        'regex' => '/picarto\.tv\/([A-Za-z0-9_-]+)/',
        'link' => 'picarto.tv'
    ],
    'twitch' => [
        'fullName' => 'Twitch.tv',
        'regex' => '/twitch\.tv\/([A-Za-z0-9_-]+)/',
        'link' => 'twitch.tv'
    ],
    'imgur' => [
        'fullName' => 'Imgur',
        'regex' => '/imgur\.com\/user\/([A-Za-z0-9_-]+)/',
        'link' => 'imgur.com/user/'
    ],
];
