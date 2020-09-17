<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use App\Models\Gallery\Gallery;
use App\Models\Gallery\GallerySubmission;

class GalleryService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Gallery Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of galleries.
    |
    */

    /**
     * Creates a new gallery.
     *
     * @param  array                  $data 
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Gallery
     */
    public function createGallery($data, $user)
    {
        DB::beginTransaction();

        try {
            $data = $this->populateData($data);
            if(!isset($data['submissions_open'])) $data['submissions_open'] = 0;
            if(!isset($data['currency_enabled'])) $data['currency_enabled'] = 0;

            $data['sort'] = Gallery::pluck('sort')->max() + 1;
            if(!isset($data['votes_required'])) $data['votes_required'] = 0;

            $gallery = Gallery::create($data);

            return $this->commitReturn($gallery);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates a gallery.
     *
     * @param  \App\Models\Gallery    $gallery
     * @param  array                  $data 
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Gallery
     */
    public function updateGallery($gallery, $data, $user)
    {
        DB::beginTransaction();

        try {
            // More specific validation
            if(Gallery::where('name', $data['name'])->where('id', '!=', $gallery->id)->exists()) throw new \Exception("The name has already been taken.");

            $data = $this->populateData($data, $gallery);
            if(!isset($data['submissions_open'])) $data['submissions_open'] = 0;
            if(!isset($data['currency_enabled'])) $data['currency_enabled'] = 0;

            $gallery->update($data);

            return $this->commitReturn($gallery);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Processes user input for creating/updating a rarity.
     *
     * @param  array               $data 
     * @param  \App\Models\Rarity  $rarity
     * @return array
     */
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
    
    /**
     * Deletes a rarity.
     *
     * @param  \App\Models\Rarity  $rarity
     * @return bool
     */
    public function deleteRarity($rarity)
    {
        DB::beginTransaction();

        try {         
            // Check first if characters with this rarity exist
            if(CharacterImage::where('rarity_id', $rarity->id)->exists() || Character::where('rarity_id', $rarity->id)->exists()) throw new \Exception("A character or character image with this rarity exists. Please change its rarity first.");

            if($rarity->has_image) $this->deleteImage($rarity->rarityImagePath, $rarity->rarityImageFileName); 
            $rarity->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Sorts rarity order.
     *
     * @param  array  $data
     * @return bool
     */
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