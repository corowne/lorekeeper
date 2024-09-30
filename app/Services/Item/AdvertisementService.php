<?php namespace App\Services\Item;

use App\Services\CharacterManager;
use App\Services\Service;

use DB;

use App\Services\InventoryManager;


class AdvertisementService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Advertisement Service
    |--------------------------------------------------------------------------
    |
    | Handles the editing and usage of Advertisement type items.
    |
    */

    /**
     * Retrieves any data that should be used in the item tag editing form.
     *
     * @return array
     */
    public function getEditData()
    {
        return [
            'choose_species' => ['0' => 'Random species', '1' => 'User chooses species'],
            'choose_trait' => ['0' => 'Random traits', '1' => 'User chooses traits']
        ];
    }

    /**
     * Processes the data attribute of the tag and returns it in the preferred format for edits.
     *
     * @param  string  $tag
     * @return mixed
     */
    public function getTagData($tag)
    {
        //fetch data from DB, if there is no data then set to NULL instead
        $AdvertisementData['choose_species'] = isset($tag->data['choose_species']) ? $tag->data['choose_species'] : null;
        $AdvertisementData['choose_trait'] = isset($tag->data['choose_trait']) ? $tag->data['choose_trait'] : null;

        return $AdvertisementData;
    }

    /**
     * Processes the data attribute of the tag and returns it in the preferred format for DB storage.
     *
     * @param  string  $tag
     * @param  array   $data
     * @return bool
     */
    public function updateData($tag, $data)
    {
        //put inputs into an array to transfer to the DB
        $AdvertisementData['choose_species'] = isset($data['choose_species']) ? $data['choose_species'] : null;
        $AdvertisementData['choose_trait'] = isset($data['choose_trait']) ? $data['choose_trait'] : null;

        DB::beginTransaction();

        try {
            //get AdvertisementData array and put it into the 'data' column of the DB for this tag
            $tag->update(['data' => json_encode($AdvertisementData)]);

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Acts upon the item when used from the inventory.
     *
     * @param  \App\Models\User\UserItem  $stacks
     * @param  \App\Models\User\User      $user
     * @param  array                      $data
     * @return bool
     */
    public function act($stacks, $user, $data)
    {
        DB::beginTransaction();

        try {
            foreach($stacks as $key=>$stack) {
                // We don't want to let anyone who isn't the owner of the advertisement to use it,
                // so do some validation...
                if($stack->user_id != $user->id) throw new \Exception("This item does not belong to you.");

                $species_id_user = (array_key_exists('species_id_adding', $data) ? $data['species_id_adding'] : null);
                $trait_id_user = (array_key_exists('feature_id_adding', $data) ? $data['feature_id_adding'] : null);

                $tagData = $stack->item->tag('advertisement')->data;
                $choose_species = (array_key_exists('choose_species', $tagData) ? $tagData['choose_species'] : "0");
                $choose_trait = (array_key_exists('choose_trait', $tagData) ? $tagData['choose_trait'] : "0");

                // Next, try to delete the tag item. If successful, we can start distributing rewards.
                if((new InventoryManager)->debitStack($stack->user, 'Advertisement Used', ['data' => ''], $stack, $data['quantities'][$key])) {
                    for($q=0; $q<$data['quantities'][$key]; $q++) {
                        $characterData = [];

                        //set user who is opening the item
                        $characterData['user_id'] = $user->id;
                        //other vital data that is default
                        $characterData['name'] = isset($characterData['name']) ? $characterData['name'] : "Advertisement " . time();
                        $characterData['transferrable_at'] = null;
                        $characterData['is_myo_slot'] = 1;
                        //this uses your default MYO slot image from the CharacterManager
                        //see wiki page for documentation on adding a default image switch
                        $characterData['use_cropper'] = 0;
                        $characterData['x0'] = null;
                        $characterData['x1'] = null;
                        $characterData['y0'] = null;
                        $characterData['y1'] = null;
                        $characterData['image'] = null;
                        $characterData['thumbnail'] = null;
                        $characterData['artist_id'][0] = null;
                        $characterData['artist_url'][0] = null;
                        $characterData['designer_id'][0] = null;
                        $characterData['designer_url'][0] = null;
                        $characterData['feature_id'][0] = null;
                        $characterData['feature_data'][0] = null;
                        $characterData['description']= '';

                        $should_use_random_species = $choose_species == 0;
                        $should_use_random_trait = $choose_trait == 0;

                        if (!$should_use_random_species && $species_id_user != null) {
                            $characterData['species_id'] = $species_id_user;
                        }
                        if (!$should_use_random_trait && $trait_id_user != null) {
                            $characterData['feature_id'] = [];
                            $characterData['feature_data'] = [];
                            $characterData['feature_id'][] = $trait_id_user;
                            $characterData['feature_data'][] = '';
                        }

                        //DB has 'true' and 'false' as strings, so need to set them to true/null
                        // TODO: (Daire) Check with Z to see if we want these to be true/false
                        $characterData['is_sellable'] = true;
                        $characterData['is_tradeable'] = true;
                        $characterData['is_giftable'] = true;
                        $characterData['is_visible'] = true;

                        // Distribute user rewards
                        $charService = new CharacterManager;
                        if ($character = $charService->createCharacter($characterData, $user, true, $should_use_random_species, $should_use_random_trait)) {
                            flash('<a href="' . $character->url . '">MYO slot</a> created successfully.')->success();
                        }
                        else {
                            throw new \Exception("Failed to use slot.");
                        }
                    }
                }
            }
            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

}
