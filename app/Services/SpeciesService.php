<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use App\Models\Species;

class SpeciesService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Species Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of character species.
    |
    */
    
    /**
     * Creates a new species.
     *
     * @param  array                  $data 
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Species
     */
    public function createSpecies($data, $user)
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

            $species = Species::create($data);

            if ($image) $this->handleImage($image, $species->speciesImagePath, $species->speciesImageFileName);

            return $this->commitReturn($species);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    /**
     * Updates a species.
     *
     * @param  \App\Models\Species    $species
     * @param  array                  $data 
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Species
     */
    public function updateSpecies($species, $data, $user)
    {
        DB::beginTransaction();

        try {
            // More specific validation
            if(Species::where('name', $data['name'])->where('id', '!=', $species->id)->exists()) throw new \Exception("The name has already been taken.");

            $data = $this->populateData($data, $species);

            $image = null;            
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }

            $species->update($data);

            if ($species) $this->handleImage($image, $species->speciesImagePath, $species->speciesImageFileName);

            return $this->commitReturn($species);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Processes user input for creating/updating a species.
     *
     * @param  array                $data 
     * @param  \App\Models\Species  $shop
     * @return array
     */
    private function populateData($data, $species = null)
    {
        if(isset($data['description']) && $data['description']) $data['parsed_description'] = parse($data['description']);
        
        if(isset($data['remove_image']))
        {
            if($species && $species->has_image && $data['remove_image']) 
            { 
                $data['has_image'] = 0; 
                $this->deleteImage($species->speciesImagePath, $species->speciesImageFileName); 
            }
            unset($data['remove_image']);
        }

        return $data;
    }
    
    /**
     * Deletes a species.
     *
     * @param  \App\Models\Species  $species
     * @return bool
     */
    public function deleteSpecies($species)
    {
        DB::beginTransaction();

        try {
            // Check first if characters with this species exists
            //if(Character::where('species_id', $species->id)->exists()) throw new \Exception("A character with this species exists. Please change its species first.");
            
            if($species->has_image) $this->deleteImage($species->speciesImagePath, $species->speciesImageFileName); 
            $species->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Sorts species order.
     *
     * @param  array  $data
     * @return bool
     */
    public function sortSpecies($data)
    {
        DB::beginTransaction();

        try {
            // explode the sort array and reverse it since the order is inverted
            $sort = array_reverse(explode(',', $data));

            foreach($sort as $key => $s) {
                Species::where('id', $s)->update(['sort' => $key]);
            }

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}