<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin Sidebar Links
    |--------------------------------------------------------------------------
    |
    | Admin sidebar links.
    |
    */

    'Users' => [
        [
            'name' => 'User Index',
            'url' => 'admin/users'
        ],
        [
            'name' => 'User Ranks',
            'url' => 'admin/users/ranks'
        ]
    ],
    'Data' => [
        [
            'name' => 'Species',
            'url' => 'admin/data/species'
        ],
        [
            'name' => 'Traits',
            'url' => 'admin/data/traits'
        ],
        [
            'name' => 'Items',
            'url' => 'admin/data/traits'
        ]
    ],
    'Characters' => [
        [
            'name' => 'Character List',
            'url' => 'admin/characters'
        ],
        [
            'name' => 'Create Character',
            'url' => 'admin/characters/create'
        ]
    ],
    'Settings' => [
        [
            'name' => 'Site Settings',
            'url' => 'admin/settings'
        ],
        [
            'name' => 'Site Images',
            'url' => 'admin/settings/images'
        ],
        [
            'name' => 'Text Pages',
            'url' => 'admin/settings/text'
        ]
    ],


];
