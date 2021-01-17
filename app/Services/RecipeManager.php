<?php namespace App\Services;

use App\Services\Service;

use DB;
use Notifications;
use Config;

use App\Models\User\User;
use App\Models\User\UserItem;
use App\Models\User\UserRecipe;

use App\Models\Recipe\Recipe;
use App\Models\Recipe\RecipeIngredient;
use App\Models\Recipe\RecipeReward;

use App\Services\InventoryManager;

class RecipeManager extends Service
{

/**********************************************************************************************
     
     RECIPE CRAFTING

 **********************************************************************************************/

    /**
     * Attempts to craft the specified recipe.
    * 
    * @param  array                        $data
    * @param  \App\Models\Recipe\Recipe    $recipe
    * @param  \App\Models\User\User        $user
    * @return bool
    */
    public function craftRecipe($data, $recipe, $user)
    {
        DB::beginTransaction();

        try {
            // Fetch the stacks from DB
            $stacks = UserItem::whereIn('id', $data['stack_id'])->get()->map(function($stack) use ($data) {
                $stack->count = (int)$data['stack_quantity'][array_search($stack->id, $data['stack_id'])];
                return $stack;
            });

            // Check for sufficient ingredients
            $plucked = $this->pluckIngredients($stacks, $recipe);
            if(!$plucked) throw new \Exception('Insufficient ingredients selected.');
            
            $service = new InventoryManager();
            // Debit the ingredients
            foreach($plucked as $id => $quantity) {
                $stack = UserItem::find($id);
                if(!$service->debitStack($user, 'Crafting', ['data' => 'Used in '.$recipe->name.' Recipe'], $stack, $quantity)) throw new Exception('Items could not be removed.');
            }

            // Credit rewards
            $logType = 'Crafting Reward';
            $craftingData = [
                'data' => 'Received rewards from '. $recipe->displayName .' recipe. (<a href="'. $recipe->url .'">View Recipe</a>)'
            ];
            
            if(!fillUserAssets($recipe->rewardItems, null, $user, $logType, $craftingData)) throw new \Exception("Failed to distribute rewards to user.");
            
            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Plucks stacks from a given Collection of user items that meet the crafting requirements of a recipe
    * If there are insufficient ingredients, null is returned
    * 
    * @param  \Illuminate\Database\Eloquent\Collection     $user_items
    * @param  \App\Models\Recipe\Recipe                    $recipe
    * @return array|null
    */
    public function pluckIngredients($user_items, $recipe)
    {
        $plucked = [];
        // foreach ingredient, search for a qualifying item, and select items up to the quantity, if insufficient continue onto the next entry
        foreach($recipe->ingredients->sortBy('ingredient_type') as $ingredient)
        {
            switch($ingredient->ingredient_type)
            {
                case 'Item':
                    $stacks = $user_items->where('item.id', $ingredient->data[0]);
                    break;
                case 'MultiItem':
                    $stacks = $user_items->whereIn('item.id', $ingredient->data);
                    break;
                case 'Category':
                    $stacks = $user_items->where('item.item_category_id', $ingredient->data[0]);
                    break;
                case 'MultiCategory':
                    $stacks = $user_items->whereIn('item.item_category_id', $ingredient->data);
                    break;
            }

                $quantity_left = $ingredient->quantity;
                while($quantity_left > 0 && count($stacks) > 0)
                {
                    $stack = $stacks->pop();
                    $plucked[$stack->id] = $stack->count >= $quantity_left ? $quantity_left : $stack->count;
                    // Update the larger collection
                    $user_items = $user_items->map(function($s) use($stack, $plucked) {
                        if($s->id == $stack->id) $s->count -= $plucked[$stack->id];
                        if($s->count) return $s;
                        else return null;
                    })->filter();
                    $quantity_left -= $plucked[$stack->id];
                }
                // If there are no more eligible ingredients but the requirement is not fulfilled, the pluck fails
                if($quantity_left > 0) return null;
            }
        return $plucked;
    }

    /**
     * Creates an recipe log.
     *
     * @param  int     $senderId
     * @param  string  $senderType
     * @param  int     $recipientId
     * @param  string  $recipientType
     * @param  int     $userRecipeId
     * @param  string  $type 
     * @param  string  $data
     * @param  int     $recipeId
     * @param  int     $quantity
     * @return  int
     */
    public function createLog($senderId, $recipientId, $characterId, $recipeId)
    {
        return DB::table('user_recipes_log')->insert(
            [
                'sender_id' => $senderId,
                'recipient_id' => $recipientId,
                'character_id' => $characterId,
                'recipe_id' => $recipeId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        );
    }
}