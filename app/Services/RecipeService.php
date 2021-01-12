<?php namespace App\Services;

use App\Services\Service;

use DB;
use Notifications;
use Config;

use App\Models\User\User;
use App\Models\User\UserRecipe;

use App\Models\Recipe\Recipe;
use App\Models\Recipe\RecipeIngredient;
use App\Models\Recipe\RecipeReward;

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
            $this->populateRewards($recipe, $data);

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

            $data = $this->populateData($data);

            $this->populateIngredients($recipe, $data);
            $this->populateRewards($recipe, $data);

            $image = null;            
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }

            $recipe->update($data);

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
            RecipeIngredient::create([
                'recipe_id' => $recipe->id,
                'ingredient_type' => $type,
                'ingredient_data' => json_encode($data['ingredient_data'][$key]),
                'quantity' => $data['ingredient_quantity'][$key]
            ]);
        }
    }

    /**
     * Manages rewards attached to the recipe
     *
     * @param  \App\Models\Recipe\Recipe   $recipe
     * @param  array                       $data 
     */
    private function populateRewards($recipe, $data)
    {
        $recipe->rewards()->delete();

        foreach($data['rewardable_type'] as $key => $type)
        {
            RecipeReward::create([
                'recipe_id' => $recipe->id,
                'rewardable_type' => $type,
                'rewardable_id' => $data['rewardable_id'][$key],
                'quantity' => $data['reward_quantity'][$key]
            ]);
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
                    if($this->creditRecipe($staff, $user, 'Staff Grant', array_only($data, ['data']), $recipe))
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
     * @param  \App\Models\User\User|\App\Models\Character\Character  $sender
     * @param  \App\Models\User\User|\App\Models\Character\Character  $recipient
     * @param  string                                                 $type 
     * @param  string                                                 $data
     * @param  \App\Models\Recipe\Recipe                            $recipe
     * @param  int                                                    $quantity
     * @return  bool
     */
    public function creditRecipe($sender, $recipient, $type, $data, $recipe)
    {
        DB::beginTransaction();

        try {
            if(is_numeric($recipe)) $recipe = Recipe::find($recipe);
            
            if($user->recipes->contains($recipe)) throw new \Exception($user->name." already has the recipe ".$recipe->displayName);
            
            $record = UserRecipe::where('user_id', $recipient->id)->where('recipe_id', $recipe->id)->first();
            if($record) {
                // Laravel doesn't support composite primary keys, so directly updating the DB row here
                DB::table('user_recipes')->where('user_id', $recipient->id)->where('recipe_id', $recipe->id);
            }
            else {
                $record = UserRecipe::create(['user_id' => $recipient->id, 'recipe_id' => $recipe->id]);
            }
            
            // if($type && !$this->createLog($sender ? $sender->id : null, $sender ? $sender->logType : null, 
            // $recipient ? $recipient->id : null, $recipient ? $recipient->logType : null, 
            // $type, $data, $recipe->id, $quantity)) throw new \Exception("Failed to create log.");

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }











    
    /**********************************************************************************************
     
        RECIPE CATEGORIES

    **********************************************************************************************/

    // /**
    //  * Create a category.
    //  *
    //  * @param  array                 $data
    //  * @param  \App\Models\User\User $user
    //  * @return \App\Models\Recipe\RecipeCategory|bool
    //  */
    // public function createRecipeCategory($data, $user)
    // {
    //     DB::beginTransaction();

    //     try {

    //         $data = $this->populateCategoryData($data);

    //         $image = null;
    //         if(isset($data['image']) && $data['image']) {
    //             $data['has_image'] = 1;
    //             $image = $data['image'];
    //             unset($data['image']);
    //         }
    //         else $data['has_image'] = 0;

    //         $category = RecipeCategory::create($data);

    //         if ($image) $this->handleImage($image, $category->categoryImagePath, $category->categoryImageFileName);

    //         return $this->commitReturn($category);
    //     } catch(\Exception $e) { 
    //         $this->setError('error', $e->getMessage());
    //     }
    //     return $this->rollbackReturn(false);
    // }

    // /**
    //  * Update a category.
    //  *
    //  * @param  \App\Models\Recipe\RecipeCategory  $category
    //  * @param  array                          $data
    //  * @param  \App\Models\User\User          $user
    //  * @return \App\Models\Recipe\RecipeCategory|bool
    //  */
    // public function updateRecipeCategory($category, $data, $user)
    // {
    //     DB::beginTransaction();

    //     try {
    //         // More specific validation
    //         if(RecipeCategory::where('name', $data['name'])->where('id', '!=', $category->id)->exists()) throw new \Exception("The name has already been taken.");

    //         $data = $this->populateCategoryData($data, $category);

    //         $image = null;            
    //         if(isset($data['image']) && $data['image']) {
    //             $data['has_image'] = 1;
    //             $image = $data['image'];
    //             unset($data['image']);
    //         }

    //         $category->update($data);

    //         if ($category) $this->handleImage($image, $category->categoryImagePath, $category->categoryImageFileName);

    //         return $this->commitReturn($category);
    //     } catch(\Exception $e) { 
    //         $this->setError('error', $e->getMessage());
    //     }
    //     return $this->rollbackReturn(false);
    // }

    // /**
    //  * Handle category data.
    //  *
    //  * @param  array                               $data
    //  * @param  \App\Models\Recipe\RecipeCategory|null  $category
    //  * @return array
    //  */
    // private function populateCategoryData($data, $category = null)
    // {
    //     if(isset($data['description']) && $data['description']) $data['parsed_description'] = parse($data['description']);
        
    //     if(isset($data['remove_image']))
    //     {
    //         if($category && $category->has_image && $data['remove_image']) 
    //         { 
    //             $data['has_image'] = 0; 
    //             $this->deleteImage($category->categoryImagePath, $category->categoryImageFileName); 
    //         }
    //         unset($data['remove_image']);
    //     }

    //     return $data;
    // }

    // /**
    //  * Delete a category.
    //  *
    //  * @param  \App\Models\Recipe\RecipeCategory  $category
    //  * @return bool
    //  */
    // public function deleteRecipeCategory($category)
    // {
    //     DB::beginTransaction();

    //     try {
    //         // Check first if the category is currently in use
    //         if(Recipe::where('recipe_category_id', $category->id)->exists()) throw new \Exception("An recipe with this category exists. Please change its category first.");
            
    //         if($category->has_image) $this->deleteImage($category->categoryImagePath, $category->categoryImageFileName); 
    //         $category->delete();

    //         return $this->commitReturn(true);
    //     } catch(\Exception $e) { 
    //         $this->setError('error', $e->getMessage());
    //     }
    //     return $this->rollbackReturn(false);
    // }

    // /**
    //  * Sorts category order.
    //  *
    //  * @param  array  $data
    //  * @return bool
    //  */
    // public function sortRecipeCategory($data)
    // {
    //     DB::beginTransaction();

    //     try {
    //         // explode the sort array and reverse it since the order is inverted
    //         $sort = array_reverse(explode(',', $data));

    //         foreach($sort as $key => $s) {
    //             RecipeCategory::where('id', $s)->update(['sort' => $key]);
    //         }

    //         return $this->commitReturn(true);
    //     } catch(\Exception $e) { 
    //         $this->setError('error', $e->getMessage());
    //     }
    //     return $this->rollbackReturn(false);
    // }
    
}