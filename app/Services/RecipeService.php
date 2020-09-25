<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use App\Models\Recipe\Recipe;

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
     
        RECIPE CATEGORIES

    **********************************************************************************************/

    /**
     * Create a category.
     *
     * @param  array                 $data
     * @param  \App\Models\User\User $user
     * @return \App\Models\Recipe\RecipeCategory|bool
     */
    public function createRecipeCategory($data, $user)
    {
        DB::beginTransaction();

        try {

            $data = $this->populateCategoryData($data);

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }
            else $data['has_image'] = 0;

            $category = RecipeCategory::create($data);

            if ($image) $this->handleImage($image, $category->categoryImagePath, $category->categoryImageFileName);

            return $this->commitReturn($category);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Update a category.
     *
     * @param  \App\Models\Recipe\RecipeCategory  $category
     * @param  array                          $data
     * @param  \App\Models\User\User          $user
     * @return \App\Models\Recipe\RecipeCategory|bool
     */
    public function updateRecipeCategory($category, $data, $user)
    {
        DB::beginTransaction();

        try {
            // More specific validation
            if(RecipeCategory::where('name', $data['name'])->where('id', '!=', $category->id)->exists()) throw new \Exception("The name has already been taken.");

            $data = $this->populateCategoryData($data, $category);

            $image = null;            
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }

            $category->update($data);

            if ($category) $this->handleImage($image, $category->categoryImagePath, $category->categoryImageFileName);

            return $this->commitReturn($category);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Handle category data.
     *
     * @param  array                               $data
     * @param  \App\Models\Recipe\RecipeCategory|null  $category
     * @return array
     */
    private function populateCategoryData($data, $category = null)
    {
        if(isset($data['description']) && $data['description']) $data['parsed_description'] = parse($data['description']);
        
        if(isset($data['remove_image']))
        {
            if($category && $category->has_image && $data['remove_image']) 
            { 
                $data['has_image'] = 0; 
                $this->deleteImage($category->categoryImagePath, $category->categoryImageFileName); 
            }
            unset($data['remove_image']);
        }

        return $data;
    }

    /**
     * Delete a category.
     *
     * @param  \App\Models\Recipe\RecipeCategory  $category
     * @return bool
     */
    public function deleteRecipeCategory($category)
    {
        DB::beginTransaction();

        try {
            // Check first if the category is currently in use
            if(Recipe::where('recipe_category_id', $category->id)->exists()) throw new \Exception("An recipe with this category exists. Please change its category first.");
            
            if($category->has_image) $this->deleteImage($category->categoryImagePath, $category->categoryImageFileName); 
            $category->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Sorts category order.
     *
     * @param  array  $data
     * @return bool
     */
    public function sortRecipeCategory($data)
    {
        DB::beginTransaction();

        try {
            // explode the sort array and reverse it since the order is inverted
            $sort = array_reverse(explode(',', $data));

            foreach($sort as $key => $s) {
                RecipeCategory::where('id', $s)->update(['sort' => $key]);
            }

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
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
            if(isset($data['recipe_category_id']) && $data['recipe_category_id'] == 'none') $data['recipe_category_id'] = null;

            if((isset($data['recipe_category_id']) && $data['recipe_category_id']) && !RecipeCategory::where('id', $data['recipe_category_id'])->exists()) throw new \Exception("The selected recipe category is invalid.");

            $data = $this->populateData($data);

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }
            else $data['has_image'] = 0;

            $recipe = Recipe::create($data);

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
        
        if(!isset($data['allow_transfer'])) $data['allow_transfer'] = 0;

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
            if(DB::table('user_recipes')->where([['recipe_id', '=', $recipe->id], ['count', '>', 0]])->exists()) throw new \Exception("At least one user currently owns this recipe. Please remove the recipe(s) before deleting it.");
            if(DB::table('loots')->where('rewardable_type', 'Recipe')->where('rewardable_id', $recipe->id)->exists()) throw new \Exception("A loot table currently distributes this recipe as a potential reward. Please remove the recipe before deleting it.");
            if(DB::table('prompt_rewards')->where('rewardable_type', 'Recipe')->where('rewardable_id', $recipe->id)->exists()) throw new \Exception("A prompt currently distributes this recipe as a reward. Please remove the recipe before deleting it.");
            if(DB::table('shop_stock')->where('recipe_id', $recipe->id)->exists()) throw new \Exception("A shop currently stocks this recipe. Please remove the recipe before deleting it.");
            
            DB::table('user_recipes_log')->where('recipe_id', $recipe->id)->delete();
            DB::table('user_recipes')->where('recipe_id', $recipe->id)->delete();
            $recipe->tags()->delete();
            if($recipe->has_image) $this->deleteImage($recipe->imagePath, $recipe->imageFileName); 
            $recipe->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    /**********************************************************************************************
     
        RECIPE TAGS

    **********************************************************************************************/
    
    /**
     * Gets a list of recipe tags for selection.
     *
     * @return array
     */
    public function getRecipeTags()
    {
        $tags = Config::get('lorekeeper.recipe_tags');
        $result = [];
        foreach($tags as $tag => $tagData)
            $result[$tag] = $tagData['name'];

        return $result;
    }
    
    /**
     * Adds an recipe tag to an recipe.
     *
     * @param  \App\Models\Recipe\Recipe  $recipe
     * @param  string                 $tag
     * @return string|bool
     */
    public function addRecipeTag($recipe, $tag)
    {
        DB::beginTransaction();

        try {
            if(!$recipe) throw new \Exception("Invalid recipe selected.");
            if($recipe->tags()->where('tag', $tag)->exists()) throw new \Exception("This recipe already has this tag attached to it.");
            if(!$tag) throw new \Exception("No tag selected.");
            
            $tag = RecipeTag::create([
                'recipe_id' => $recipe->id,
                'tag' => $tag
            ]);

            return $this->commitReturn($tag);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    /**
     * Edits the data associated with an recipe tag on an recipe.
     *
     * @param  \App\Models\Recipe\Recipe  $recipe
     * @param  string                 $tag
     * @param  array                  $data
     * @return string|bool
     */
    public function editRecipeTag($recipe, $tag, $data)
    {
        DB::beginTransaction();

        try {
            if(!$recipe) throw new \Exception("Invalid recipe selected.");
            if(!$recipe->tags()->where('tag', $tag)->exists()) throw new \Exception("This recipe does not have this tag attached to it.");
            
            $tag = $recipe->tags()->where('tag', $tag)->first();

            $service = $tag->service;
            if(!$service->updateData($tag, $data)) {
                $this->setErrors($service->errors());
                throw new \Exception('sdlfk');
            }

            // Update the tag's active setting
            $tag->is_active = isset($data['is_active']);
            $tag->save();

            return $this->commitReturn($tag);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    /**
     * Removes an recipe tag from an recipe.
     *
     * @param  \App\Models\Recipe\Recipe  $recipe
     * @param  string                 $tag
     * @return string|bool
     */
    public function deleteRecipeTag($recipe, $tag)
    {
        DB::beginTransaction();

        try {
            if(!$recipe) throw new \Exception("Invalid recipe selected.");
            if(!$recipe->tags()->where('tag', $tag)->exists()) throw new \Exception("This recipe does not have this tag attached to it.");
            
            $recipe->tags()->where('tag', $tag)->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}