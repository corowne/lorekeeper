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
    'navbar_news_notif' => 0,

    // Species Trait Index - Mercury
    'species_trait_index' => 0,

    // Character Status Badges - Juni
    'character_status_badges' => 0,
    
    // Character TH Profile Link - Juni
    'character_TH_profile_link' => 0,

    // Design Update Voting - Mercury
    'design_update_voting' => 0,

    // Item Entry Expansion - Mercury
    'item_entry_expansion' => [
        'extra_fields' => 0,
        'resale_function' => 0,
    ]

];
