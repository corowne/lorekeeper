<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Currency\Currency;
use App\Models\Item\ItemCategory;
use App\Models\Item\Item;
use App\Models\Encounters\Encounters;

class EncounterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Prompts Controller
    |--------------------------------------------------------------------------
    |
    */

    /**
     * Shows the encounters page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('encounters.encounters');
    }
}


