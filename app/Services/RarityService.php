<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use App\Models\Rarity;

class RarityService extends Service
{

    public function createRarity($data, $user)
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

            $rarity = Rarity::create($data);

            if ($image) $this->handleImage($image, $rarity->rarityImagePath, $rarity->rarityImageFileName);

            return $this->commitReturn($rarity);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    public function updateRarity($rarity, $data, $user)
    {
        DB::beginTransaction();

        try {
            // More specific validation
            if(Rarity::where('name', $data['name'])->where('id', '!=', $rarity->id)->exists()) throw new \Exception("The name has already been taken.");

            $data = $this->populateData($data, $rarity);

            $image = null;            
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }

            $rarity->update($data);

            if ($rarity) $this->handleImage($image, $rarity->rarityImagePath, $rarity->rarityImageFileName);

            return $this->commitReturn($rarity);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    private function populateData($data, $rarity = null)
    {
        if(isset($data['description']) && $data['description']) $data['parsed_description'] = parse($data['description']);

        if(isset($data['color'])) $data['color'] = str_replace('#', '', $data['color']);
        
        if(isset($data['remove_image']))
        {
            if($rarity && $rarity->has_image && $data['remove_image']) 
            { 
                $data['has_image'] = 0; 
                $this->deleteImage($rarity->rarityImagePath, $rarity->rarityImageFileName); 
            }
            unset($data['remove_image']);
        }

        return $data;
    }
    
    public function deleteRarity($rarity)
    {
        DB::beginTransaction();

        try {
            // Check first if the rarity is currently in use
            //if(Character::where('rarity_id', $rarity->id)->exists()) throw new \Exception("A character with this rarity exists. Please change its rarity first.");
            //if(Feature::where('rarity_id', $rarity->id)->exists()) throw new \Exception("A character with this rarity exists. Please change its rarity first.");
            
            if($rarity->has_image) $this->deleteImage($rarity->rarityImagePath, $rarity->rarityImageFileName); 
            $rarity->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    public function sortRarity($data)
    {
        DB::beginTransaction();

        try {
            // explode the sort array and reverse it since the order is inverted
            $sort = array_reverse(explode(',', $data));

            foreach($sort as $key => $s) {
                Rarity::where('id', $s)->update(['sort' => $key]);
            }

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}