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
            'name' => 'rarities',
            'url' => 'admin/data/rarities'
        ],
        [
            'name' => 'Species',
            'url' => 'admin/data/species'
        ],
        [
            'name' => 'Trait Categories',
            'url' => 'admin/data/trait-categories'
        ],
        [
            'name' => 'Traits',
            'url' => 'admin/data/traits'
        ],
        [
            'name' => 'Item Categories',
            'url' => 'admin/data/item-categories'
        ],
        [
            'name' => 'Items',
            'url' => 'admin/data/items'
        ],
        [
            'name' => 'Currencies',
            'url' => 'admin/data/currencies'
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
