<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Currency\Currency;
use App\Models\Rarity;
use App\Models\Species;
use App\Models\Item\ItemCategory;
use App\Models\Feature\FeatureCategory;

class WorldController extends Controller
{
    /**
     * Show the index page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('world.index', [  
        ]);
    }

    /**
     * Show the currency page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCurrencies(Request $request)
    {
        $query = Currency::query();
        $name = $request->get('name');
        if($name) $query->where('name', 'LIKE', '%'.$name.'%')->orWhere('abbreviation', 'LIKE', '%'.$name.'%');
        return view('world.currencies', [  
            'currencies' => $query->orderBy('name')->paginate(20),
        ]);
    }

    /**
     * Show the rarity page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getRarities(Request $request)
    {
        $query = Rarity::query();
        $name = $request->get('name');
        if($name) $query->where('name', 'LIKE', '%'.$name.'%');
        return view('world.rarities', [  
            'rarities' => $query->orderBy('sort', 'DESC')->paginate(20),
        ]);
    }

    /**
     * Show the species page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSpecieses(Request $request)
    {
        $query = Species::query();
        $name = $request->get('name');
        if($name) $query->where('name', 'LIKE', '%'.$name.'%');
        return view('world.specieses', [  
            'specieses' => $query->orderBy('sort', 'DESC')->paginate(20),
        ]);
    }
    
    /**
     * Show the item categories page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getItemCategories(Request $request)
    {
        $query = ItemCategory::query();
        $name = $request->get('name');
        if($name) $query->where('name', 'LIKE', '%'.$name.'%');
        return view('world.item_categories', [  
            'categories' => $query->orderBy('sort', 'DESC')->paginate(20),
        ]);
    }
    
    /**
     * Show the trait categories page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getFeatureCategories(Request $request)
    {
        $query = FeatureCategory::query();
        $name = $request->get('name');
        if($name) $query->where('name', 'LIKE', '%'.$name.'%');
        return view('world.feature_categories', [  
            'categories' => $query->orderBy('sort', 'DESC')->paginate(20),
        ]);
    }
}
