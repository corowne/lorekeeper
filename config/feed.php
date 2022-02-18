<?php

return [
    'feeds' => [
        'news' => [
            /*
             * Here you can specify which class and method will return
             * the items that should appear in the feed. For example:
             * 'App\Model@getAllFeedItems'
             *
             * You can also pass an argument to that method:
             * ['App\Model@getAllFeedItems', 'argument']
             */
            'items' => 'App\Models\News@getFeedItems',

            /*
             * The feed will be available on this url.
             */
            'url' => '/news',

            'title'       => env('APP_NAME', 'Laravel').' ・ News',
            'description' => 'Site news.',
            'language'    => 'en-US',

            /*
             * The view that will render the feed.
             */
            'view' => 'feed::atom',

            /*
             * The type to be used in the <link> tag
             */
            'type' => 'application/atom+xml',
        ],

        'sales' => [
            /*
             * Here you can specify which class and method will return
             * the items that should appear in the feed. For example:
             * 'App\Model@getAllFeedItems'
             *
             * You can also pass an argument to that method:
             * ['App\Model@getAllFeedItems', 'argument']
             */
            'items' => 'App\Models\Sales\Sales@getFeedItems',

            /*
             * The feed will be available on this url.
             */
            'url' => '/sales',

            'title'       => env('APP_NAME', 'Laravel').' ・ Sales',
            'description' => 'Site news.',
            'language'    => 'en-US',

            /*
             * The view that will render the feed.
             */
            'view' => 'feed::atom',

            /*
             * The type to be used in the <link> tag
             */
            'type' => 'application/atom+xml',
        ],
    ],
];
