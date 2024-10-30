<?php

/*
|--------------------------------------------------------------------------
| Mentions
|--------------------------------------------------------------------------
|
| These are settings that affect, specifically, how mentions are used.
| Mentions are basically shorthand codes mainly for staff to quickly
| refer to a user, a character or a galleryimage.
|
| Over time, these mentions have expanded to encompass quite a few items,
| and in this current update, the sheer number of items that can be
| 'mentioned' well over doubled, and this may cause annoyances for staff.
| As such, a seperate configuration file was made for staff to choose, or
| rather, mix and match effectively, which they want to use and which they
| want displayed on site.
|
| These are not expected to be changed often or on short schedule and are
| therefore separate from the settings modifiable in the admin panel.
|
| Each setting has an 'enable' and a 'show_text' option.
| 'enable' turns the option on if set to 1. (To turn it off, set it to 0.)
| 'show_text' displays the "Mention This" block on-site if turned on.
| (The same 0 for off and 1 for on applies there, too.)
|
| Note that 'show_text' is ignored if 'enable' is set to 0, as it makes
|  no sense to display how to use the function if you cannot use it.
|
| Finally, please note that changing any of these settings require you to
| re-edit text blocks in which they are used, as the mentions are generated
| upon hitting the final submit button.
|
| This means that disabling an older function will NOT automatically
| remove previous instances of it being used, either.
|
*/

return [

    /*
    |--------------------------------------------------------------------------
    | User Mentions
    |--------------------------------------------------------------------------
    |
    | This is the original method of mentioning a user. This is used to link
    | directly to a user's profile page, and give the name the appropriate
    | rank icon and coloration.
    |
    | Usage:
    | @username
    | - Replace username with the user's username.
    |
    | This option has been enabled by default for backwards compatability.
    |
    */
    'user_mention'     => [
        'enable'    => 1,
        'show_text' => 1,
    ],

];
