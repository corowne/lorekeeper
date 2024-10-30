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
    | directly to a user's profile page, giving the name the appropriate
    | rank icon and coloration.
    |
    | Usage:
    | @username
    | - Replace username with the user's username.
    |
    | This option has been enabled by default for backwards compatibility.
    |
    */
    'user_mention'              => [
        'enable'    => 1,
        'show_text' => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | User and Avatar Mentions
    |--------------------------------------------------------------------------
    |
    | This is a newer method of mentioning a user. This is used to link
    | directly to a user's profile page, giving the name the appropriate
    | rank icon and coloration, and adds the user's avatar in front.
    |
    | Usage:
    | %username
    | - Replace username with the user's username.
    |
    | This option has been enabled by default for backwards compatibility.
    |
    */
    'user_and_avatar_mention'   => [
        'enable'    => 1,
        'show_text' => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | User Permalinks
    |--------------------------------------------------------------------------
    |
    | The previous methods are affected negatively by username changes.
    | This method has the same end result, but instead use a bbcode-like tag
    | with the user's id to create permanent links.
    | This is used to link directly to a user's profile page,
    | giving the name the appropriate rank icon and coloration.
    |
    | Usage:
    | [user=id]
    | - Replace id with the user's id.
    |
    | This option has been enabled by default for backwards compatibility.
    |
    */
    'user_permalink'            => [
        'enable'    => 1,
        'show_text' => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | User Avatar Permalinks
    |--------------------------------------------------------------------------
    |
    | Similar to the User Permalinks, this method uses a bbcode-like tag
    | with the user's id to create permanent links.
    | This method only displays the user's avatar.
    |
    | Usage:
    | [userav=id]
    | - Replace id with the user's id.
    |
    | This option has been enabled by default for backwards compatibility.
    |
    */
    'user_avatar_permalink'     => [
        'enable'    => 1,
        'show_text' => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | Character Permalinks
    |--------------------------------------------------------------------------
    |
    | This mention is to link to specific characters using their slug.
    | This is used to link directly to a character's masterlist entry,
    | displaying the slug and name of the character.
    |
    | Usage:
    | [character=slug]
    | - Replace slug with the character's slug.
    |
    | This option has been enabled by default for backwards compatibility.
    |
    */
    'character_permalink'       => [
        'enable'    => 1,
        'show_text' => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | Character Thumbnail Permalinks
    |--------------------------------------------------------------------------
    |
    | Similar to the Character Permalinks, this is used to link to the
    | character's masterlist entry, instead of the slug and name, however,
    | it displays the character's thumbnail image.
    |
    | Usage:
    | [charthumb=slug]
    | - Replace slug with the character's slug.
    |
    | This option has been enabled by default for backwards compatibility.
    |
    */
    'character_thumb_permalink' => [
        'enable'    => 1,
        'show_text' => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | Gallery Thumbnail Permalinks
    |--------------------------------------------------------------------------
    |
    | A method to display the thumbnail for gallery submissions.
    | This displays either a thumbnail for the submission's image or, if it
    | has no image, displays a short version of the written text instead.
    |
    | Usage:
    | [thumb=id]
    | - Replace id with the gallery submission's id.
    |
    | This option has been enabled by default for backwards compatibility.
    |
    */
    'gallery_thumb_permalink'   => [
        'enable'    => 1,
        'show_text' => 1,
    ],

];
