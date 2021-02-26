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

    'dA' => [
        'fullName' => 'deviantArt',
        'regex' => '/deviantart\.com\/([A-Za-z0-9_-]+)/'
    ],

    /**********************************************************************************************
     
        GENERAL

    **********************************************************************************************/

    'twitter' => [
        'fullName' => 'Twitter',
        'regex' => '/twitter\.com\/([A-Za-z0-9_-]+)/'
    ],
    'ig' => [
        'fullName' => 'instagram',
        'regex' => '/instagram\.com\/([A-Za-z0-9_-]+)/'
    ],
    'tumblr' => [
        'fullName' => 'Tumblr',
        'regex' => '/([A-Za-z0-9_-]+)\.tumblr\.com/'
    ],
    'TH' => [
        'fullName' => 'Toyhou.se',
        'regex' => '/toyhou\.se\/([A-Za-z0-9_-]+)/'
    ],
    'artstation' => [
        'fullName' => 'Artstation',
        'regex' => '/artstation\.com\/([A-Za-z0-9_-]+)/'
    ],
    'picarto' => [
        'fullName' => 'Picarto',
        'regex' => '/picarto\.tv\/([A-Za-z0-9_-]+)/'
    ],
    'twitch' => [
        'fullName' => 'Twitch.tv',
        'regex' => '/twitch\.tv\/([A-Za-z0-9_-]+)/'
    ],
    'imgur' => [
        'fullName' => 'imgur',
        'regex' => '/imgur\.com\/user\/([A-Za-z0-9_-]+)/'
    ],
];
