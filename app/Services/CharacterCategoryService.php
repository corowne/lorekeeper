<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use App\Models\Character\Character;
use App\Models\Character\CharacterCategory;
use App\Models\Character\CharacterLineageBlacklist;

class CharacterCategoryService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Character Category Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of character categories.
    |
    */

    /**
     * Create a category.
     *
     * @param  array  $data
     * @return \App\Models\Character\CharacterCategory|bool
     */
    public function createCharacterCategory($data)
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

            $category = CharacterCategory::create($data);
            CharacterLineageBlacklist::searchAndSet($data['lineage-blacklist'], 'category', $category->id);

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
     * @param  \App\Models\Character\CharacterCategory  $category
     * @param  array                                    $data
     * @return \App\Models\Character\CharacterCategory|bool
     */
    public function updateCharacterCategory($category, $data)
    {
        DB::beginTransaction();

        try {
            if(CharacterCategory::where('name', $data['name'])->where('id', '!=', $category->id)->exists()) throw new \Exception("The name has already been taken.");
            if(CharacterCategory::where('code', $data['code'])->where('id', '!=', $category->id)->exists()) throw new \Exception("The code has already been taken.");

            $data = $this->populateCategoryData($data, $category);
            $blacklist = CharacterLineageBlacklist::searchAndSet($data['lineage-blacklist'], 'category', $category->id);

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
     * @param  array                                         $data
     * @param  \App\Models\Character\CharacterCategory|null  $category
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
     * @param  \App\Models\Character\CharacterCategory  $category
     * @return bool
     */
    public function deleteCharacterCategory($category)
    {
        DB::beginTransaction();

        try {
            // Check first if the category is currently in use
            if(Character::where('character_category_id', $category->id)->exists()) throw new \Exception("An character with this category exists. Please change its category first.");

            if($category->has_image) $this->deleteImage($category->categoryImagePath, $category->categoryImageFileName);
            $category->delete();

            // delete associated blacklist, if one exists.
            CharacterLineageBlacklist::searchAndSet(0, 'category', $category->id);

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
    public function sortCharacterCategory($data)
    {
        DB::beginTransaction();

        try {
            // explode the sort array and reverse it since the order is inverted
            $sort = array_reverse(explode(',', $data));

            foreach($sort as $key => $s) {
                CharacterCategory::where('id', $s)->update(['sort' => $key]);
            }

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}
