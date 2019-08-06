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

    'Admin' => [
        'power' => 'admin',
        'links' => [
            [
                'name' => 'User Ranks',
                'url' => 'admin/users/ranks'
            ]
        ]
    ],
    'Site' => [
        'power' => 'edit_pages',
        'links' => [
            [
                'name' => 'News',
                'url' => 'admin/news'
            ],
            [
                'name' => 'Pages',
                'url' => 'admin/pages'
            ]
        ]
    ],
    'Users' => [
        'power' => 'edit_user_info',
        'links' => [
            [
                'name' => 'User Index',
                'url' => 'admin/users'
            ],
            [
                'name' => 'Invitation Keys',
                'url' => 'admin/invitations'
            ],
        ]
    ],
    'Queues' => [
        'power' => 'manage_submissions',
        'links' => [
            [
                'name' => 'Prompt Submissions',
                'url' => 'admin/submissions/pending'
            ],
        ]
    ],
    'Grants' => [
        'power' => 'edit_inventories',
        'links' => [
            [
                'name' => 'Currency Grants',
                'url' => 'admin/grants/user-currency'
            ],
            [
                'name' => 'Item Grants',
                'url' => 'admin/grants/items'
            ],
        ]
    ],
    'Data' => [
        'power' => 'edit_data',
        'links' => [
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
                'name' => 'Prompt Categories',
                'url' => 'admin/data/prompt-categories'
            ],
            [
                'name' => 'Prompts',
                'url' => 'admin/data/prompts'
            ],
            [
                'name' => 'Character Categories',
                'url' => 'admin/data/character-categories'
            ],
            [
                'name' => 'Currencies',
                'url' => 'admin/data/currencies'
            ],
            [
                'name' => 'Loot Tables',
                'url' => 'admin/data/loot-tables'
            ],
        ]
    ],
    'Masterlist' => [
        'power' => 'manage_characters',
        'links' => [
            [
                'name' => 'Masterlist',
                'url' => 'admin/characters'
            ],
            [
                'name' => 'Create Character',
                'url' => 'admin/masterlist/create-character'
            ],
            [
                'name' => 'Character Transfers',
                'url' => 'admin/masterlist/transfers/incoming'
            ]
        ]
    ],
    'Raffles' => [
        'power' => 'manage_raffles',
        'links' => [
            [
                'name' => 'Raffles',
                'url' => 'admin/raffles'
            ],
        ]
    ],
    'Settings' => [
        'power' => 'edit_site_settings',
        'links' => [
            [
                'name' => 'Site Settings',
                'url' => 'admin/settings'
            ],
            [
                'name' => 'Site Images',
                'url' => 'admin/images'
            ],
        ]
    ],


];
