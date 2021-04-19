<?php namespace App\Services\Item;

use App\Services\Service;

use DB;

use App\Services\InventoryManager;

use App\Models\Item\Item;
use App\Models\Currency\Currency;
use App\Models\Loot\LootTable;
use App\Models\Raffle\Raffle;
use App\Models\Recipe\Recipe;

class RecipeService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Recipe Service
    |--------------------------------------------------------------------------
    |
    | Handles the editing and usage of box type items.
    |
    */

    /**
     * Retrieves any data that should be used in the item tag editing form.
     *
     * @return array
     */
    public function getEditData()
    {
        return [
            'recipes'=> Recipe::orderBy('name')->where('needs_unlocking',1)->pluck('name', 'id'),
        ];
    }

    /**
     * Processes the data attribute of the tag and returns it in the preferred format.
     *
     * @param  string  $tag
     * @return mixed
     */
    public function getTagData($tag)
    {
        if(isset($tag->data['all_recipes'])) return 'All';
        // if($tag->data)
        $rewards = [];
        if($tag->data) {
            $assets = parseAssetData($tag->data);
            foreach($assets as $type => $a)
            {
                $class = getAssetModelString($type, false);
                foreach($a as $id => $asset)
                {
                    $rewards[] = (object)[
                        'rewardable_type' => $class,
                        'rewardable_id' => $id,
                        'quantity' => $asset['quantity']
                    ];
                }
            }
        }
        return $rewards;
    }

    /**
     * Processes the data attribute of the tag and returns it in the preferred format.
     *
     * @param  string  $tag
     * @param  array   $data
     * @return bool
     */
    public function updateData($tag, $data)
    {
        DB::beginTransaction();

        try {
            // If there's no data, return.
            if(!isset($data['rewardable_id']) && !isset($data['all_recipes'])) return true;
            if(isset($data['all_recipes'])) $assets = ['all_recipes' => 1];
            else {
                // The data will be stored as an asset table, json_encode()d.
                // First build the asset table, then prepare it for storage.

                $type = 'App\Models\Recipe\Recipe';

                $assets = createAssetsArray();
                foreach($data['rewardable_id'] as $key => $r) {
                    $asset = $type::find($data['rewardable_id'][$key]);
                    addAsset($assets, $asset, $data['quantity'][$key]);
                }
                $assets = getDataReadyAssets($assets);
            }

            $tag->update(['data' => json_encode($assets)]);

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }


    /**
     * Acts upon the item when used from the inventory.
     *
     * @param  \App\Models\User\UserItem  $stacks
     * @param  \App\Models\User\User      $user
     * @param  array                      $data
     * @return bool
     */
    public function act($stacks, $user, $data)
    {
        DB::beginTransaction();

        try {
            $firstData = $stacks->first()->item->tag('recipe')->data;
            if(isset($firstData['all_recipes']) && $firstData['all_recipes']){
                $recipeOptions = Recipe::where('needs_unlocking',1)->whereNotIn('id',$user->recipes->pluck('id')->toArray())->get();
            }
            elseif(isset($firstData['recipes']) && count($firstData['recipes'])) {
                $recipeOptions = Recipe::find(array_keys($firstData['recipes']))->where('needs_unlocking',1)->whereNotIn('id',$user->recipes->pluck('id')->toArray());
            }

            $options = $recipeOptions->pluck('id')->toArray();
            if(!count($options)) throw new \Exception("There are no more options for this recipe redemption item.");
            if(count($options) < array_sum($data['quantities'])) throw new \Exception("You have selected a quantity too high for the quantity of recipes you can unlock with this item.");

            foreach($stacks as $key=>$stack) {

                // We don't want to let anyone who isn't the owner of the box open it,
                // so do some validation...
                if($stack->user_id != $user->id) throw new \Exception("This item does not belong to you.");

                // Next, try to delete the box item. If successful, we can start distributing rewards.
                if((new InventoryManager)->debitStack($stack->user, 'Recipe Redeemed', ['data' => ''], $stack, $data['quantities'][$key])) {
                    for($q=0; $q<$data['quantities'][$key]; $q++) {

                        $random = array_rand($options);
                        $thisRecipe['recipes'] = [ $options[$random] => 1 ];
                        unset($options[$random]);

                        // Distribute user rewards
                        if(!$rewards = fillUserAssets(parseAssetData($thisRecipe), $user, $user, 'Recipe Redemption', [
                            'data' => 'Redeemed from '.$stack->item->name
                        ])) throw new \Exception("Failed to open recipe redemption item.");
                        flash($this->getRecipeRewardsString($rewards));
                    }
                }
            }
            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Acts upon the item when used from the inventory.
     *
     * @param  array                  $rewards
     * @return string
     */
    private function getRecipeRewardsString($rewards)
    {
        $results = "You have unlocked the following recipe: ";
        $result_elements = [];
        foreach($rewards as $assetType)
        {
            if(isset($assetType))
            {
                foreach($assetType as $asset)
                {
                    array_push($result_elements, $asset['asset']->displayName);
                }
            }
        }
        return $results.implode(', ', $result_elements);
    }
}
