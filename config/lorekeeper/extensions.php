<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Extensions
    |--------------------------------------------------------------------------
    |
    | This enables/disables a selection of extensions which provide QoL and are
    | broadly applicable, but perhaps not universally, and which are contained
    | in scope enough to be readily opt-in.
    |
    | Extensions with a single value for their setting are enabled/disabled via it
    | and have no additional configuration necessary here. 0 = disabled, 1 = enabled.
    | All of the extensions here are disabled by default.
    |
    | Please refer to the readme for more information on each of these extensions.
    |
    */

    // Navbar News Notif - Juni
    'navbar_news_notif'                    => 0,

    // Species Trait Index - Mercury
    'species_trait_index'                  => [
        'enable'       => 0,
        'trait_modals' => 0, // Enables modals when you click on a trait for more info instead of linking to the traits page - Moif
    ],

    // Character Status Badges - Juni
    'character_status_badges'              => 0,

    // Character TH Profile Link - Juni
    'character_TH_profile_link'            => 0,

    // Design Update Voting - Mercury
    'design_update_voting'                 => 0,

    // Item Entry Expansion - Mercury
    'item_entry_expansion'                 => [
        'extra_fields'    => 0,
        'resale_function' => 0,
        'loot_tables'     => [
            // Adds the ability to use either rarity criteria for items or item categories with rarity criteria in loot tables. Note that disabling this does not apply retroactively.
            'enable'              => 0,
            'alternate_filtering' => 0, // By default this uses more broadly compatible methods to filter by rarity. If you are on Dreamhost/know your DB software can handle searching in JSON, it's recommended to set this to 1 instead.
        ],
    ],

    // Group Traits By Category - Uri
    'traits_by_category'                   => 0,

    // Scroll To Top - Uri
    'scroll_to_top'                        => 0, // 1 - On, 0 - off

    // Character Reward Expansion - Uri
    'character_reward_expansion'           => [
        'expanded'          => 1,
        'default_recipient' => 0, // 0 to default to the character's owner (if a user), 1 to default to the submission user.
    ],

    // MYO Image Hide/Remove - Mercury
    // Adds an option when approving MYO submissions to hide or delete the MYO placeholder image
    'remove_myo_image'                     => 0,

    // Auto-populate New Image Traits - Mercury
    // Automatically adds the traits present on a character's active image to the list when uploading a new image for an extant character.
    'autopopulate_image_features'          => 0,

    // Staff Rewards - Mercury
    'staff_rewards'                        => [
        'enabled'     => 0,
        'currency_id' => 1,
    ],

    // Organised Traits Dropdown - Draginraptor
    'organised_traits_dropdown'            => 0,

    // Previous & Next buttons on Character pages - Speedy
    // Adds buttons linking to the previous character as well as the next character on all character pages.
    'previous_and_next_characters' => [
        'display' => 0,
        'reverse' => 0, // By default, 0 has the lower number on the 'Next' side and the higher number on the 'Previous' side, reflecting the default masterlist order. Setting this to 1 reverses this.
    ],

    // Aliases on Userpage - Speedy
    'aliases_on_userpage' => 0, // By default, does not display the aliases on userpage. Enable to add a small arrow to display these underneath the primary alias.

    // Show All Recent Submissions - Speedy
    'show_all_recent_submissions' => [
        'enable' => 0,
        'links'  => [
            'sidebar'      => 1,      // By default, ON, and will display in the sidebar.
            'indexbutton'  => 1, // By default, ON, and will display a button on the index.
        ],
        'section_on_front' => 0, // By default, does not display on the front page. Enable to add a block above the footer.
    ],

    // collapsible admin sidebar - Newt
    'collapsible_admin_sidebar' => 0,

    // use gravatar for user avatars - Newt
    'use_gravatar' => 0,

    // Use ReCaptcha to check new user registrations - Mercury
    // Requires site key and secret be set in your .env file!
    'use_recaptcha' => 0,
];
