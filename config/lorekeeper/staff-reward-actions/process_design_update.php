<?php

return [
    // Name and description shown in the admin panel
    'name'        => 'Design Update Processing',
    'description' => 'Approving or rejecting design updates. Doesn\'t include cancelling.',
    'actions'     => [
        // Actions, as reported to admin action logging,
        // that should use this key's reward value
        'Rejected Design Update',
        'Approved Design Update',
    ],
];
