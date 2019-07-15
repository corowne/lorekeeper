<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use App\Models\Prompt\PromptCategory;
use App\Models\Prompt\Prompt;
use App\Models\Prompt\PromptReward;

class PromptService extends Service
{
    /**********************************************************************************************
     
        PROMPT CATEGORIES

    **********************************************************************************************/
    public function createPromptCategory($data, $user)
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

            $category = PromptCategory::create($data);

            if ($image) $this->handleImage($image, $category->categoryImagePath, $category->categoryImageFileName);

            return $this->commitReturn($category);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    public function updatePromptCategory($category, $data, $user)
    {
        DB::beginTransaction();

        try {
            // More specific validation
            if(PromptCategory::where('name', $data['name'])->where('id', '!=', $category->id)->exists()) throw new \Exception("The name has already been taken.");

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
    
    public function deletePromptCategory($category)
    {
        DB::beginTransaction();

        try {
            // Check first if the category is currently in use
            if(Prompt::where('prompt_category_id', $category->id)->exists()) throw new \Exception("An prompt with this category exists. Please change its category first.");
            
            if($category->has_image) $this->deleteImage($category->categoryImagePath, $category->categoryImageFileName); 
            $category->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    public function sortPromptCategory($data)
    {
        DB::beginTransaction();

        try {
            // explode the sort array and reverse it since the order is inverted
            $sort = array_reverse(explode(',', $data));

            foreach($sort as $key => $s) {
                PromptCategory::where('id', $s)->update(['sort' => $key]);
            }

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    
    /**********************************************************************************************
     
        PROMPTS

    **********************************************************************************************/

    public function createPrompt($data, $user)
    {
        DB::beginTransaction();

        try {
            if(isset($data['prompt_category_id']) && $data['prompt_category_id'] == 'none') $data['prompt_category_id'] = null;

            if((isset($data['prompt_category_id']) && $data['prompt_category_id']) && !PromptCategory::where('id', $data['prompt_category_id'])->exists()) throw new \Exception("The selected prompt category is invalid.");

            $data = $this->populateData($data);

            $prompt = Prompt::create(array_only($data, ['prompt_category_id', 'name', 'summary', 'description', 'parsed_description', 'is_active', 'start_at', 'end_at', 'hide_before_start', 'hide_after_end']));

            $this->populateRewards($prompt, array_only($data, ['rewardable_type', 'rewardable_id', 'quantity']));

            return $this->commitReturn($prompt);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    public function updatePrompt($prompt, $data, $user)
    {
        DB::beginTransaction();

        try {
            if(isset($data['prompt_category_id']) && $data['prompt_category_id'] == 'none') $data['prompt_category_id'] = null;

            // More specific validation
            if(Prompt::where('name', $data['name'])->where('id', '!=', $prompt->id)->exists()) throw new \Exception("The name has already been taken.");
            if((isset($data['prompt_category_id']) && $data['prompt_category_id']) && !PromptCategory::where('id', $data['prompt_category_id'])->exists()) throw new \Exception("The selected prompt category is invalid.");

            $data = $this->populateData($data);

            $prompt->update(array_only($data, ['prompt_category_id', 'name', 'summary', 'description', 'parsed_description', 'is_active', 'start_at', 'end_at', 'hide_before_start', 'hide_after_end']));

            $this->populateRewards($prompt, array_only($data, ['rewardable_type', 'rewardable_id', 'quantity']));

            return $this->commitReturn($prompt);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    private function populateData($data, $prompt = null)
    {
        if(isset($data['description']) && $data['description']) $data['parsed_description'] = parse($data['description']);
        
        if(!isset($data['hide_before_start'])) $data['hide_before_start'] = 0;
        if(!isset($data['hide_after_end'])) $data['hide_after_end'] = 0;
        if(!isset($data['is_active'])) $data['is_active'] = 0;

        return $data;
    }

    private function populateRewards($prompt, $data)
    {
        // Clear the old rewards...
        $prompt->rewards()->delete();

        foreach($data['rewardable_type'] as $key => $type)
        {
            PromptReward::create([
                'prompt_id'       => $prompt->id,
                'rewardable_type' => $type,
                'rewardable_id'   => $data['rewardable_id'][$key],
                'quantity'        => $data['quantity'][$key],
            ]);
        }
    }
    
    public function deletePrompt($prompt)
    {
        DB::beginTransaction();

        try {
            // Check first if there are submissions under the prompt
             
            $prompt->rewards()->delete();
            $prompt->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}