<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Rotating Site Header
    |--------------------------------------------------------------------------
    |
    | Used to determine what site header image to use on a given month and/or day.
    |
    */

    // This is the default image to use!
    0 => [
        // null means the site's default header will be used!
        0 => null,
    ],

    12 => [
        // this is december's default header!
        0 => 'images/header.png',

        // null means the month's default header will be used!
        7 => null,

        // this specific header will show up on december 9th!
        9 => 'images/header.png',
    ],

];
