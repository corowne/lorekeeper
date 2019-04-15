<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Currency\Currency;
use App\Models\Rarity;
use App\Models\Species;
use App\Models\Item\ItemCategory;
use App\Models\Item\Item;
use App\Models\Feature\FeatureCategory;
use App\Models\Feature\Feature;

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
    
    /**
     * Show the traits page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getFeatures(Request $request)
    {
        $query = Feature::query();
        $data = $request->only(['rarity_id', 'feature_category_id', 'species_id', 'name', 'sort']);
        if(isset($data['rarity_id']) && $data['rarity_id'] != 'none') 
            $query->where('rarity_id', $data['rarity_id']);
        if(isset($data['feature_category_id']) && $data['feature_category_id'] != 'none') 
            $query->where('feature_category_id', $data['feature_category_id']);
        if(isset($data['species_id']) && $data['species_id'] != 'none') 
            $query->where('species_id', $data['species_id']);
        if(isset($data['name'])) 
            $query->where('name', 'LIKE', '%'.$data['name'].'%');

        if(isset($data['sort'])) 
        {
            switch($data['sort']) {
                case 'alpha':
                    $query->sortAlphabetical();
                    break;
                case 'alpha-reverse':
                    $query->sortAlphabetical(true);
                    break;
                case 'category':
                    $query->sortCategory();
                    break;
                case 'rarity':
                    $query->sortRarity();
                    break;
                case 'rarity-reverse':
                    $query->sortRarity(true);
                    break;
                case 'species':
                    $query->sortSpecies();
                    break;
                case 'newest':
                    $query->sortNewest();
                    break;
                case 'oldest':
                    $query->sortOldest();
                    break;
            }
        } 
        else $query->sortCategory();

        return view('world.features', [
            'features' => $query->paginate(20),
            'rarities' => ['none' => 'Any Rarity'] + Rarity::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'specieses' => ['none' => 'Any Species'] + Species::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'categories' => ['none' => 'Any Category'] + FeatureCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray()
        ]);
    }

    /**
     * Show the items page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getItems(Request $request)
    {
        $query = Item::query();
        $data = $request->only(['item_category_id', 'name', 'sort']);
        if(isset($data['item_category_id']) && $data['item_category_id'] != 'none') 
            $query->where('item_category_id', $data['item_category_id']);
        if(isset($data['name'])) 
            $query->where('name', 'LIKE', '%'.$data['name'].'%');

        if(isset($data['sort'])) 
        {
            switch($data['sort']) {
                case 'alpha':
                    $query->sortAlphabetical();
                    break;
                case 'alpha-reverse':
                    $query->sortAlphabetical(true);
                    break;
                case 'category':
                    $query->sortCategory();
                    break;
                case 'newest':
                    $query->sortNewest();
                    break;
                case 'oldest':
                    $query->sortOldest();
                    break;
            }
        } 
        else $query->sortCategory();

        return view('world.items', [
            'items' => $query->paginate(20),
            'categories' => ['none' => 'Any Category'] + ItemCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray()
        ]);
    }
    
}
