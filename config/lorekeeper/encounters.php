<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Encounters Settings
    |--------------------------------------------------------------------------
    |
    | Edit this file to change certain settings related to encounters
    | 
    |
    */

    //self explanatory
    //set to 1 to use characters instead of user stats
    //this will debit/grant or check things from the character instead
    'use_characters' => 0, 
    //self explanatory
    //if turned off it will try to use currency instead
    'use_energy' => 1,
    //id of currency to use instead of energy if above is turned off
    'energy_replacement_id' => 1,

    //turn off if you want to turn off energy gen automatically
    //either that or remove the kernel command
    'refresh_energy' => 1,

];
