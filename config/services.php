<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\Models\User\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],

    'deviantart' => [
        'client_id' => env('DEVIANTART_CLIENT_ID'),
        'client_secret' => env('DEVIANTART_CLIENT_SECRET'),
        'redirect' => env('DEVIANTART_REDIRECT_URI', '/auth/callback/deviantart'),
    ],

    'twitter' => [
        'client_id' => env('TWITTER_CLIENT_ID'),
        'client_secret' => env('TWITTER_CLIENT_SECRET'),
        'redirect' => env('TWITTER_REDIRECT_URI', '/auth/callback/twitter'),
    ],

    'instagram' => [
        'client_id' => env('INSTAGRAM_CLIENT_ID'),  
        'client_secret' => env('INSTAGRAM_CLIENT_SECRET'),  
        'redirect' => env('INSTAGRAM_REDIRECT_URI', '/auth/callback/instagram') 
    ],

    'tumblr' => [
        'client_id' => env('TUMBLR_CLIENT_ID'),  
        'client_secret' => env('TUMBLR_CLIENT_SECRET'),  
        'redirect' => env('TUMBLR_REDIRECT_URI', '/auth/callback/tumblr') 
    ],

    'imgur' => [
        'client_id' => env('IMGUR_CLIENT_ID'),  
        'client_secret' => env('IMGUR_CLIENT_SECRET'),  
        'redirect' => env('IMGUR_REDIRECT_URI', '/auth/callback/imgur') 
    ],

    'twitch' => [    
        'client_id' => env('TWITCH_CLIENT_ID'),  
        'client_secret' => env('TWITCH_CLIENT_SECRET'),  
        'redirect' => env('TWITCH_REDIRECT_URI', '/auth/callback/twitch') 
    ],

    'discord' => [    
        'client_id' => env('DISCORD_CLIENT_ID'),  
        'client_secret' => env('DISCORD_CLIENT_SECRET'),  
        'redirect' => env('DISCORD_REDIRECT_URI', '/auth/callback/discord'),
      ],
];
