<?php namespace App\Services;

use Carbon\Carbon;
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
use App\Models\Recipe\RecipeLimit;

use App\Services\InventoryManager;

class RecipeService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Recipe Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of recipe categories and recipes.
    |
    */

    /**********************************************************************************************
     
        RECIPES

    **********************************************************************************************/

    /**
     * Creates a new recipe.
     *
     * @param  array                  $data 
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Recipe\Recipe
     */
    public function createRecipe($data, $user)
    {
        DB::beginTransaction();

        try {
            // if(isset($data['recipe_category_id']) && $data['recipe_category_id'] == 'none') $data['recipe_category_id'] = null;

            // if((isset($data['recipe_category_id']) && $data['recipe_category_id']) && !RecipeCategory::where('id', $data['recipe_category_id'])->exists()) throw new \Exception("The selected recipe category is invalid.");

            if(!isset($data['ingredient_type'])) throw new \Exception('Please add at least one ingredient.');
            if(!isset($data['rewardable_type'])) throw new \Exception('Please add at least one reward to the recipe.');

            $data = $this->populateData($data);

            foreach($data['ingredient_type'] as $key => $type)
            {
                if(!$type) throw new \Exception("Ingredient type is required.");
                if(!$data['ingredient_data'][$key]) throw new \Exception("Ingredient data is required.");
                if(!$data['ingredient_quantity'][$key] || $data['ingredient_quantity'][$key] < 1) throw new \Exception("Quantity is required and must be an integer greater than 0.");
            }

            foreach($data['rewardable_type'] as $key => $type)
            {
                if(!$type) throw new \Exception("Reward type is required.");
                if(!$data['rewardable_id'][$key]) throw new \Exception("Reward is required.");
                if(!$data['reward_quantity'][$key] || $data['reward_quantity'][$key] < 1) throw new \Exception("Quantity is required and must be an integer greater than 0.");
            }

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }
            else $data['has_image'] = 0;

            $recipe = Recipe::create($data);
            $this->populateIngredients($recipe, $data);
            //limits
            $this->populateLimits($recipe, $data);

            $recipe->output = $this->populateRewards($data);
            $recipe->save();

            if ($image) $this->handleImage($image, $recipe->imagePath, $recipe->imageFileName);

            return $this->commitReturn($recipe);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates an recipe.
     *
     * @param  \App\Models\Recipe\Recipe  $recipe
     * @param  array                  $data 
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Recipe\Recipe
     */
    public function updateRecipe($recipe, $data, $user)
    {
        DB::beginTransaction();

        try {
            if(isset($data['recipe_category_id']) && $data['recipe_category_id'] == 'none') $data['recipe_category_id'] = null;

            // More specific validation
            if(Recipe::where('name', $data['name'])->where('id', '!=', $recipe->id)->exists()) throw new \Exception("The name has already been taken.");
            if((isset($data['recipe_category_id']) && $data['recipe_category_id']) && !RecipeCategory::where('id', $data['recipe_category_id'])->exists()) throw new \Exception("The selected recipe category is invalid.");

            if(!isset($data['ingredient_type'])) throw new \Exception('Please add at least one ingredient.');
            if(!isset($data['rewardable_type'])) throw new \Exception('Please add at least one reward to the recipe.');

            $data = $this->populateData($data);
            $this->populateIngredients($recipe, $data);
            // do limits
            $this->populateLimits($recipe, $data);

            $image = null;            
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }

            $recipe->update($data);
            $recipe->output = $this->populateRewards($data);
            $recipe->save();

            if ($recipe) $this->handleImage($image, $recipe->imagePath, $recipe->imageFileName);

            return $this->commitReturn($recipe);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Processes user input for creating/updating an recipe.
     *
     * @param  array                  $data 
     * @param  \App\Models\Recipe\Recipe  $recipe
     * @return array
     */
    private function populateData($data, $recipe = null)
    {
        if(isset($data['description']) && $data['description']) $data['parsed_description'] = parse($data['description']);

        if(isset($data['needs_unlocking']) && $data['needs_unlocking']) $data['needs_unlocking'] = 1;
        else $data['needs_unlocking'] = 0;

        if(isset($data['remove_image']))
        {
            if($recipe && $recipe->has_image && $data['remove_image']) 
            { 
                $data['has_image'] = 0; 
                $this->deleteImage($recipe->imagePath, $recipe->imageFileName); 
            }
            unset($data['remove_image']);
        }

        return $data;
    }
    
    /**
     * Manages ingredients attached to the recipe
     *
     * @param  \App\Models\Recipe\Recipe   $recipe
     * @param  array                       $data 
     */
    private function populateIngredients($recipe, $data)
    {
        $recipe->ingredients()->delete();

        foreach($data['ingredient_type'] as $key => $type)
        {
            if(!count(array_filter($data['ingredient_data'][$key]))) throw new \Exception('One of the ingredients was not specified.');
            RecipeIngredient::create([
                'recipe_id' => $recipe->id,
                'ingredient_type' => $type,
                'ingredient_data' => json_encode($data['ingredient_data'][$key]),
                'quantity' => $data['ingredient_quantity'][$key]
            ]);
        }
    }

    /**
     * Creates the assets json from rewards
     *
     * @param  \App\Models\Recipe\Recipe   $recipe
     * @param  array                       $data 
     */
    private function populateRewards($data)
    {
        if(isset($data['rewardable_type'])) {
            // The data will be stored as an asset table, json_encode()d. 
            // First build the asset table, then prepare it for storage.
            $assets = createAssetsArray();
            foreach($data['rewardable_type'] as $key => $r) {
                switch ($r)
                {
                    case 'Item':
                        $type = 'App\Models\Item\Item';
                        break;
                    case 'Currency':
                        $type = 'App\Models\Currency\Currency';
                        break;
                    case 'LootTable':
                        $type = 'App\Models\Loot\LootTable';
                        break;
                    case 'Raffle':
                        $type = 'App\Models\Raffle\Raffle';
                        break;
                }
                $asset = $type::find($data['rewardable_id'][$key]);
                addAsset($assets, $asset, $data['reward_quantity'][$key]);
            }
            
            return getDataReadyAssets($assets);
        }
        return null;
    }

    /**
     * Adds limits to the recipe
     *
     * @param  \App\Models\Recipe\Recipe   $recipe
     * @param  array                       $data 
     */
    private function populateLimits($recipe, $data)
    {
        if(!isset($data['is_limited'])) $data['is_limited'] = 0;

        $recipe->is_limited = $data['is_limited'];
        $recipe->save();

        $recipe->limits()->delete();

        if(isset($data['limit_type'])) {
            foreach($data['limit_type'] as $key => $type)
            {
                if(!isset($data['limit_id'][$key])) throw new \Exception('One of the limits was not specified.');
                RecipeLimit::create([
                    'recipe_id'  => $recipe->id,
                    'limit_type' => $type,
                    'limit_id'   => $data['limit_id'][$key],
                    'quantity'   => $data['limit_quantity'][$key],
                ]);
            }
        }
    }

    /**
     * Deletes an recipe.
     *
     * @param  \App\Models\Recipe\Recipe  $recipe
     * @return bool
     */
    public function deleteRecipe($recipe)
    {
        DB::beginTransaction();

        try {
            // Check first if the recipe is currently owned or if some other site feature uses it
            if(DB::table('user_recipes')->where('recipe_id', $recipe->id)->exists()) throw new \Exception("At least one user currently owns this recipe. Please remove the recipe(s) before deleting it.");
            if(DB::table('loots')->where('rewardable_type', 'Recipe')->where('rewardable_id', $recipe->id)->exists()) throw new \Exception("A loot table currently distributes this recipe as a potential reward. Please remove the recipe before deleting it.");
            if(DB::table('prompt_rewards')->where('rewardable_type', 'Recipe')->where('rewardable_id', $recipe->id)->exists()) throw new \Exception("A prompt currently distributes this recipe as a reward. Please remove the recipe before deleting it.");
            // FIXME if(DB::table('shop_stock')->where('recipe_id', $recipe->id)->exists()) throw new \Exception("A shop currently stocks this recipe. Please remove the recipe before deleting it.");
            
            DB::table('user_recipes_log')->where('recipe_id', $recipe->id)->delete();
            DB::table('user_recipes')->where('recipe_id', $recipe->id)->delete();
            // FIXME $recipe->tags()->delete();
            if($recipe->has_image) $this->deleteImage($recipe->imagePath, $recipe->imageFileName); 
            $recipe->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    
    /**********************************************************************************************
     
        RECIPE GRANTS

    **********************************************************************************************/

    /**
     * Admin function for granting recipes to multiple users.
     *
     * @param  array                  $data
     * @param  \App\Models\User\User  $staff
     * @return  bool
     */
    public function grantRecipes($data, $staff)
    {
        DB::beginTransaction();

        try {
            // Process names
            $users = User::find($data['names']);
            if(count($users) != count($data['names'])) throw new \Exception("An invalid user was selected.");

            // Process recipes
            $recipes = Recipe::find($data['recipe_ids']);
            if(!$recipes) throw new \Exception("Invalid recipes selected.");

            foreach($users as $user) {
                foreach($recipes as $recipe) {   
                    if($this->creditRecipe($staff, $user, null, 'Staff Grant', array_only($data, ['data']), $recipe))
                    {
                        Notifications::create('RECIPE_GRANT', $user, [
                            'recipe_name' => $recipe->name,
                            'sender_url' => $staff->url,
                            'sender_name' => $staff->name
                        ]);
                    }
                    else
                    {
                        throw new \Exception("Failed to credit recipes to ".$user->name.".");
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
     * Credits recipe to a user or character.
     *
     * @param  \App\Models\User\User                        $sender
     * @param  \App\Models\User\User                        $recipient
     * @param  \App\Models\Character\Character              $character
     * @param  string                                       $type 
     * @param  string                                       $data
     * @param  \App\Models\Recipe\Recipe                    $recipe
     * @param  int                                          $quantity
     * @return  bool
     */
    public function creditRecipe($sender, $recipient, $character, $type, $data, $recipe)
    {
        DB::beginTransaction();

        try {
            if(is_numeric($recipe)) $recipe = Recipe::find($recipe);
            
            // if($recipient->recipes->contains($recipe)) throw new \Exception($recipient->name." already has the recipe ".$recipe->displayName);
            if($recipient->recipes->contains($recipe)) {
                flash($recipient->name." already has the recipe ".$recipe->displayName, 'warning');
                return $this->commitReturn(false);
            }
            
            $record = UserRecipe::where('user_id', $recipient->id)->where('recipe_id', $recipe->id)->first();
            if($record) {
                // Laravel doesn't support composite primary keys, so directly updating the DB row here
                DB::table('user_recipes')->where('user_id', $recipient->id)->where('recipe_id', $recipe->id);
            }
            else {
                $record = UserRecipe::create(['user_id' => $recipient->id, 'recipe_id' => $recipe->id]);
            }
            
            if($type && !$this->createLog($sender ? $sender->id : null, $recipient ? $recipient->id : null,
            $character ? $character->id : null, $type, $data['data'], $recipe->id)) throw new \Exception("Failed to create log.");

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
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
     * @return  int
     */
    public function createLog($senderId, $recipientId, $characterId, $type, $data, $recipeId)
    {
        return DB::table('user_recipes_log')->insert(
            [
                'sender_id' => $senderId,
                'recipient_id' => $recipientId,
                'character_id' => $characterId,
                'recipe_id' => $recipeId,
                'log' => $type . ($data ? ' (' . $data . ')' : ''),
                'log_type' => $type,
                'data' => $data, // this should be just a string
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        );
    }
}
