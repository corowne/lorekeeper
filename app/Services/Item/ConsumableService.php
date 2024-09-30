<?php namespace App\Services\Item;

use App\Models\Character\Character;
use App\Models\Character\CharacterFeature;
use App\Models\Species\Species;
use App\Services\CharacterManager;
use App\Services\Service;
use Illuminate\Http\Request;

use DB;

use App\Services\InventoryManager;

use App\Models\Feature\Feature;
use App\Models\Item\Item;
use App\Models\Rarity;

class ConsumableService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Consumable Service
    |--------------------------------------------------------------------------
    |
    | Handles the editing and usage of Consumable type items.
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
            'trait_added' => ['0' => 'Select Trait'] + Feature::orderBy('name', 'DESC')->pluck('name', 'id')->toArray(),
            'trait_removed' => ['0' => 'Select Trait'] + Feature::orderBy('name', 'DESC')->pluck('name', 'id')->toArray(),
            'reroll_species' => ['0' => 'Do not reroll species', '1' => 'Reroll species'],
            'reroll_traits' => ['0' => 'Do not reroll traits', '1' => 'Reroll traits'],
            'add_specific_trait' => ['0' => 'Do not add specific trait', '1' => 'Add specific trait'],
            'remove_specific_trait' => ['0' => 'Do not remove specific trait', '1' => 'Remove specific trait'],
            'reroll_specific_trait' => ['0' => 'Do not reroll specific trait', '1' => 'Reroll specific trait'],
            'reroll_specific_species' => ['0' => 'Do not reroll specific species', '1' => 'Reroll specific species']
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
        $consumableData['trait_added'] = isset($tag->data['trait_added']) ? $tag->data['trait_added'] : null;
        $consumableData['trait_removed'] = isset($tag->data['trait_removed']) ? $tag->data['trait_removed'] : null;
        $consumableData['reroll_species'] = isset($tag->data['reroll_species']) ? $tag->data['reroll_species'] : null;
        $consumableData['reroll_traits'] = isset($tag->data['reroll_traits']) ? $tag->data['reroll_traits'] : null;
        $consumableData['add_specific_trait'] = isset($tag->data['add_specific_trait']) ? $tag->data['add_specific_trait'] : null;
        $consumableData['remove_specific_trait'] = isset($tag->data['remove_specific_trait']) ? $tag->data['remove_specific_trait'] : null;
        $consumableData['reroll_specific_trait'] = isset($tag->data['reroll_specific_trait']) ? $tag->data['reroll_specific_trait'] : null;
        $consumableData['reroll_specific_species'] = isset($tag->data['reroll_specific_species']) ? $tag->data['reroll_specific_species'] : null;

        return $consumableData;
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
        $consumableData['trait_added'] = isset($data['trait_added']) ? $data['trait_added'] : null;
        $consumableData['trait_removed'] = isset($data['trait_removed']) ? $data['trait_removed'] : null;
        $consumableData['reroll_species'] = isset($data['reroll_species']) ? $data['reroll_species'] : null;
        $consumableData['reroll_traits'] = isset($data['reroll_traits']) ? $data['reroll_traits'] : null;
        $consumableData['add_specific_trait'] = isset($data['add_specific_trait']) ? $data['add_specific_trait'] : null;
        $consumableData['remove_specific_trait'] = isset($data['remove_specific_trait']) ? $data['remove_specific_trait'] : null;
        $consumableData['reroll_specific_trait'] = isset($data['reroll_specific_trait']) ? $data['reroll_specific_trait'] : null;
        $consumableData['reroll_specific_species'] = isset($data['reroll_specific_species']) ? $data['reroll_specific_species'] : null;

        DB::beginTransaction();

        try {
            //get consumableData array and put it into the 'data' column of the DB for this tag
            $tag->update(['data' => json_encode($consumableData)]);

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
            // throw new \Exception(json_encode($data));

            foreach($stacks as $key=>$stack) {
                if ($data['character_id_affected'] == null) {
                    throw new \Exception("You must select a character to use the consumable on.");
                }

                // We don't want to let anyone who isn't the owner of the Consumable to use it, so do some validation...
                // $character = $user->characters()->where('id', "=", $data['character_id_affected'])->first();
                // Find where ID is the character ID and user_id is the user's ID
                $character = Character::find($data['character_id_affected']);
                if($character == null || $character->user_id != $user->id) {
                    throw new \Exception("You do not own this consumable.");
                }

                $quantity = $data['quantities'][$key];
                $tagData = $stack->item->tag('consumable')->data;

                $trait_adding = $tagData['trait_added'];
                $trait_removing = $tagData['trait_removed'];
                $reroll_species = (array_key_exists('reroll_species', $tagData) ? $tagData['reroll_species'] : "0");
                $reroll_traits = (array_key_exists('reroll_traits', $tagData) ? $tagData['reroll_traits'] : "0");
                $add_specific_trait = (array_key_exists('add_specific_trait', $tagData) ? $tagData['add_specific_trait'] : "0");
                $remove_specific_trait = (array_key_exists('remove_specific_trait', $tagData) ? $tagData['remove_specific_trait'] : "0");
                $reroll_specific_trait = (array_key_exists('reroll_specific_trait', $tagData) ? $tagData['reroll_specific_trait'] : "0");
                $reroll_specific_species = (array_key_exists('reroll_specific_species', $tagData) ? $tagData['reroll_specific_species'] : "0");

                // NOTE: (Daire) If modifying traits, we only want to use one consumable at a time regardless of the quantity specified.
                if ($trait_adding != 0 || $trait_removing != 0 || $reroll_species != 0|| $reroll_traits != 0 || $add_specific_trait != 0 || $remove_specific_trait != 0 || $reroll_specific_trait != 0)
                {
                    $quantity = "1";
                }

                // Next, try to delete the tag item. If successful, we can start distributing rewards.
                if((new InventoryManager)->debitStack($stack->character, 'Consumable Used', ['data' => ''], $stack, $quantity)) {
                    for($q=0; $q<$quantity; $q++) {                        
                        if ($trait_adding == 0 && $trait_removing == 0 && $reroll_species == 0 && $reroll_traits == 0 && $add_specific_trait == 0 && $remove_specific_trait == 0 && $reroll_specific_trait == 0 && $reroll_specific_species == 0)
                        {
                            throw new \Exception("No action specified for Consumable.");
                        }

                        if ($trait_adding != 0)
                        {
                            CharacterManager::AddTraitToCharacter($character, $trait_adding, true);
                        }
                        
                        if ($trait_removing != 0)
                        {
                            CharacterManager::RemoveTraitFromCharacter($character, $trait_removing, true);
                        }

                        if ($reroll_species != 0)
                        {
                            $this->actRerollAllSpecies($character);
                        }

                        if ($reroll_traits != 0)
                        {
                            CharacterManager::RerollAllTraitsOnCharacter($character);
                        }


                        if ($add_specific_trait != 0)
                        {
                            $trait_adding_user = $data['feature_id_adding'];
                            CharacterManager::AddTraitToCharacter($character, $trait_adding_user, true);
                        }

                        if ($remove_specific_trait != 0)
                        {
                            $trait_removing_user = $data['feature_id_removing'];
                            CharacterManager::RemoveTraitFromCharacter($character, $trait_removing_user);
                            $this->addItemThatAddsTraitToUser($trait_removing_user, $user);
                        }

                        if ($reroll_specific_trait != 0)
                        {
                            $trait_rerolling_user = $data['feature_id_rerolling'];
                            CharacterManager::RerollTraitOnCharacter($character, $trait_rerolling_user, true);
                        }

                        if ($reroll_specific_species != 0)
                        {
                            $species_rerolling_user = $data['species_id_rerolling'];
                            $this->actRerollSpecies($character, $species_rerolling_user);
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

    // TODO: (Daire) Move these to the CharacterManager incase we need to re-use the logic.
    private function actRerollAllSpecies($character)
    {
        $existingSpeciesIds = [];

        // Reroll base species
        $randomSpeciesPrimary = CharacterManager::getRandomSpecies(Species::all(), $existingSpeciesIds);
        $character->image->update(['species_id' => $randomSpeciesPrimary->id]);

        // Reroll secondary species if present
        if ($character->image->secondary_species_id != null) {
            $existingSpeciesIds[] = $randomSpeciesPrimary->id;
    
            $randomSpeciesSecondary = CharacterManager::getRandomSpecies(Species::all(), $existingSpeciesIds);
            $character->image->update(['secondary_species_id' => $randomSpeciesSecondary->id]);
        }
    }

    private function actRerollSpecies($character, $species_id)
    {
        // Get a list of all species the character already has
        $existingSpeciesIds = [];
        if ($character->image->species_id != null) {
            $existingSpeciesIds[] = $character->image->species_id;
        }
        if ($character->image->secondary_species_id != null) {
            $existingSpeciesIds[] = $character->image->secondary_species_id;
        }

        // Check if we are rerolling the primary or secondary species
        $isTargetingSecondary = false;
        if ($character->image->species_id == $species_id) {
            $isTargetingSecondary = false;
        } else if ($character->image->secondary_species_id == $species_id) {
            $isTargetingSecondary = true;
        } else {
            throw new \Exception("Character does not have this species.");
        }

        // Get a list of all species that exist in the game except the current ones
        $randomSpecies = CharacterManager::getRandomSpecies(Species::all(), $existingSpeciesIds);

        if ($randomSpecies == null) { throw new \Exception("No species found to reroll into."); }
        
        if ($isTargetingSecondary) {
            $character->image->update(['secondary_species_id' => $randomSpecies->id]);
            
        } else {
            $character->image->update(['species_id' => $randomSpecies->id]);
        }
    }

    private function addItemThatAddsTraitToCharacter($trait_removing_user, $character)
    {
        // Find an item that contains a consumable tag which adds the speified trait ID. Tags are one to many childs of the item.
        $item = Item::whereHas('tags', function($query) use ($trait_removing_user) {
            $query->where('data->trait_added', $trait_removing_user);
        })->first();

        if (!$item) { throw new \Exception("No item found that adds the specified trait."); }

        $stack = (new InventoryManager)->creditItem(null, $character, 'Added from consumable', ['data' => ''], $item, 1);
    }

    private function addItemThatAddsTraitToUser($trait_removing_user, $user)
    {
        // Find an item that contains a consumable tag which adds the speified trait ID. Tags are one to many childs of the item.
        $item = Item::whereHas('tags', function($query) use ($trait_removing_user) {
            $query->where('data->trait_added', $trait_removing_user);
        })->first();

        if (!$item) { throw new \Exception("No item found that adds the specified trait."); }

        $stack = (new InventoryManager)->creditItem(null, $user, 'Added from consumable', ['data' => ''], $item, 1);
    }

}
