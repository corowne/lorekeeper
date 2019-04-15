<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use App\Models\Feature\FeatureCategory;
use App\Models\Feature\Feature;

class FeatureService extends Service
{
    /**********************************************************************************************
     
        FEATURE CATEGORIES

    **********************************************************************************************/
    public function createFeatureCategory($data, $user)
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

            $category = FeatureCategory::create($data);

            if ($image) $this->handleImage($image, $category->categoryImagePath, $category->categoryImageFileName);

            return $this->commitReturn($category);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    public function updateFeatureCategory($category, $data, $user)
    {
        DB::beginTransaction();

        try {
            // More specific validation
            if(FeatureCategory::where('name', $data['name'])->where('id', '!=', $category->id)->exists()) throw new \Exception("The name has already been taken.");

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
    
    public function deleteFeatureCategory($category)
    {
        DB::beginTransaction();

        try {
            // Check first if the category is currently in use
            if(Feature::where('feature_category_id', $category->id)->exists()) throw new \Exception("A trait with this category exists. Please change its category first.");
            
            if($category->has_image) $this->deleteImage($category->categoryImagePath, $category->categoryImageFileName); 
            $category->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    public function sortFeatureCategory($data)
    {
        DB::beginTransaction();

        try {
            // explode the sort array and reverse it since the order is inverted
            $sort = array_reverse(explode(',', $data));

            foreach($sort as $key => $s) {
                FeatureCategory::where('id', $s)->update(['sort' => $key]);
            }

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    
    /**********************************************************************************************
     
        FEATURES

    **********************************************************************************************/

    public function createFeature($data, $user)
    {
        DB::beginTransaction();

        try {
            $data = $this->populateData($data);

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }
            else $data['has_image'] = 0;

            $feature = Feature::create($data);

            if ($image) $this->handleImage($image, $feature->imagePath, $feature->imageFileName);

            return $this->commitReturn($feature);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    public function updateFeature($feature, $data, $user)
    {
        DB::beginTransaction();

        try {
            // More specific validation
            if(Feature::where('name', $data['name'])->where('id', '!=', $feature->id)->exists()) throw new \Exception("The name has already been taken.");

            $data = $this->populateData($data);

            $image = null;            
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }

            $feature->update($data);

            if ($feature) $this->handleImage($image, $feature->imagePath, $feature->imageFileName);

            return $this->commitReturn($feature);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    private function populateData($data, $feature = null)
    {
        if(isset($data['description']) && $data['description']) $data['parsed_description'] = parse($data['description']);
        if(isset($data['species_id']) && $data['species_id'] == 'none') $data['species_id'] = null;
        if(isset($data['feature_category_id']) && $data['feature_category_id'] == 'none') $data['feature_category_id'] = null;
        if(isset($data['remove_image']))
        {
            if($feature && $feature->has_image && $data['remove_image']) 
            { 
                $data['has_image'] = 0; 
                $this->deleteImage($feature->imagePath, $feature->imageFileName); 
            }
            unset($data['remove_image']);
        }

        return $data;
    }
    
    public function deleteFeature($feature)
    {
        DB::beginTransaction();

        try {
            // Check first if the feature is currently in use
            if(DB::table('character_features')->where('feature_id', $feature->id)->exists()) throw new \Exception("A character with this trait exists. Please remove the trait first.");
            
            if($feature->has_image) $this->deleteImage($feature->imagePath, $feature->imageFileName); 
            $feature->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}