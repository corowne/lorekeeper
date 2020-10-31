<?php

/*
|--------------------------------------------------------------------------
| Settings
|--------------------------------------------------------------------------
|
| These are settings that affect how the site works.
| These are not expected to be changed often or on short schedule and are 
| therefore separate from the settings modifiable in the admin panel.
| It's highly recommended that you do any required modifications to this file
| as well as config/app.php before you start using the site.
|
*/

return [
    
    /*
    |--------------------------------------------------------------------------
    | Site Name
    |--------------------------------------------------------------------------
    |
    | This differs from the app name in that it is allowed to contain spaces
    | (APP_NAME in .env cannot take spaces). This will be displayed on the
    | site wherever the name needs to be displayed.
    |
    */
    'site_name' => 'Lorekeeper',

    /*
    |--------------------------------------------------------------------------
    | Site Description
    |--------------------------------------------------------------------------
    |
    | This is the description used for the site in meta tags-- previews
    | displayed on various social media sites, discord, and the like.
    | It is not, however, displayed on the site itself. This should be kept short and snappy!
    |
    */
    'site_desc' => 'A Lorekeeper ARPG',
    
    /*
    |--------------------------------------------------------------------------
    | Character Codes
    |--------------------------------------------------------------------------
    |
    | character_codes:
    |       This is used in the automatic generation of character codes.
    |       {category}: This is replaced by the character category code.
    |       {number}: This is replaced by the character number.
    |
    |       e.g. Under the default setting ({category}-{number}), 
    |       a character in a category called "MYO" (code "MYO") with number 001
    |       will have the character code of MYO-001.
    |
    |       !IMPORTANT!
    |       As this is used to generate the character's URL, sticking to 
    |       alphanumeric, hyphen (-) and underscore (_) characters
    |       is advised.
    |
    | character_number_digits: 
    |       This specifies the default number of digits for {number} when
    |       pulled automatically. 
    |
    |       e.g. If the next number is 2, setting this to 3 would give 002.
    |
    | character_pull_number: 
    |       This determines if the next {number} is pulled from the highest
    |       existing number, or the highest number in the category.
    |       This value can be "all" (default) or "category".
    |       
    |       e.g. if the following characters exist:
    |       Standard (STD) category: STD-001, STD-002, STD-003
    |       MYO (MYO) category:      MYO-001, MYO-002 
    |       If character_pull_number is 'all': 
    |           The next number pulled will be 004 regardless of category.
    |       If character_pull_number is 'category':
    |           The next number pulled for STD will be 004.
    |           The next number pulled for MYO will be 003. 
    |
    | reset_character_status_on_transfer:
    |       This determines whether owner-set character status--
    |       trading, gift art, and gift writing--
    |       should be cleared when the character is transferred to a new owner.
    |       Default: 0/Disabled, 1 to enable.
    |
    | reset_character_profile_on_transfer:
    |       This determines whether character name and profile should be cleared
    |       when the character is transferred to a new owner.
    |       Default: 0/Disabled, 1 to enable.
    |
    | clear_myo_slot_name_on_approval:
    |       Whether the "name" given to a MYO slot should be cleared when a design update for it is approved/
    |       the slot becomes a full character.
    |       Default: 0/Disabled, 1 to enable.
    |
    */
    'character_codes' => '{category}-{number}',
    'character_number_digits' => 3,
    'character_pull_number' => 'all',

    'reset_character_status_on_transfer' => 0,
    'reset_character_profile_on_transfer' => 0,
    'clear_myo_slot_name_on_approval' => 0,

    /*
    |--------------------------------------------------------------------------
    | Masterlist Thumbnail Dimensions
    |--------------------------------------------------------------------------
    |
    | This affects the dimensions used by the character thumbnail cropper.
    | Using a smallish size is recommended to reduce the amount of time
    | needed to load the masterlist pages.
    |
    */
    'masterlist_thumbnails' => [
        'width' => 200,
        'height' => 200
    ],

    /*
    |--------------------------------------------------------------------------
    | Trade Asset Limit
    |--------------------------------------------------------------------------
    |
    | This is an arbitrary upper limit on how many things (items, currencies,
    | characters) a trade can contain. While this can potentially be higher,
    | there are limits on data storage, so raising this is not recommended.
    |
    */
    'trade_asset_limit' => 20
];
