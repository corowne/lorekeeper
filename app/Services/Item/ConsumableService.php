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

                // NOTE: (Daire) If modifying traits, we only want to use one consumable at a time regardless of the quantity specified.
                if ($trait_adding != 0 || $trait_removing != 0 || $reroll_species != 0|| $reroll_traits != 0 || $add_specific_trait != 0 || $remove_specific_trait != 0 || $reroll_specific_trait != 0)
                {
                    $quantity = "1";
                }

                // Next, try to delete the tag item. If successful, we can start distributing rewards.
                if((new InventoryManager)->debitStack($stack->character, 'Consumable Used', ['data' => ''], $stack, $quantity)) {
                    for($q=0; $q<$quantity; $q++) {                        
                        if ($trait_adding == 0 && $trait_removing == 0 && $reroll_species == 0 && $reroll_traits == 0 && $add_specific_trait == 0 && $remove_specific_trait == 0 && $reroll_specific_trait == 0)
                        {
                            throw new \Exception("No action specified for Consumable.");
                        }

                        if ($trait_adding != 0)
                        {
                            $this->actAddTrait($trait_adding, $character);
                        }
                        
                        if ($trait_removing != 0)
                        {
                            $this->actRemoveTrait($trait_removing, $character, true);
                        }

                        if ($add_specific_trait != 0)
                        {
                            $trait_adding_user = $data['feature_id_adding'];
                            $this->actAddTrait($trait_adding_user, $character);
                        }
                        
                        if ($remove_specific_trait != 0)
                        {
                            $trait_removing_user = $data['feature_id_removing'];
                            $this->actRemoveTrait($trait_removing_user, $character, false);
                            $this->addItemThatAddsTraitToUser($trait_removing_user, $user);
                        }

                        if ($reroll_species != 0)
                        {
                            $this->actRerollSpecies($character);
                        }

                        if ($reroll_traits != 0)
                        {
                            $this->actRerollAllTraits($character);
                        }

                        if ($reroll_specific_trait != 0)
                        {
                            $trait_rerolling_user = $data['feature_id_rerolling'];
                            $this->actRerollTrait($trait_rerolling_user, $character);
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

    private function actAddTrait($trait_adding, $character)
    {
        // Check that the trait exists
        $trait = Feature::find($trait_adding);
        if (!$trait) { throw new \Exception("Trait not found."); }

        // Check if the character already has the trait
        if ($character->image->features->contains('feature_id', $trait_adding)) { throw new \Exception("Character already has this trait."); }

        // Add the trait to the character
        $feature = CharacterFeature::create(['character_image_id' => $character->image->id, 'feature_id' => $trait_adding, 'data' => 'Added from a consumable']);

        // Return the feature
        return $feature;
    }

    private function actRemoveTrait($trait_removing, $character, $canRemoveRestricted)
    {
        // Check that the trait exists
        $trait = Feature::find($trait_removing);
        if (!$trait) { throw new \Exception("Trait not found."); }
        $restricted_traits = [ "Mutation" ];
        if (!$canRemoveRestricted && in_array($trait->name, $restricted_traits)) { throw new \Exception("Cannot remove a restricted trait."); }

        // Check if the character has the trait
        if (!$character->image->features->contains('feature_id', $trait_removing)) { throw new \Exception("Character does not have this trait."); }

        $matching_trait = CharacterFeature::where('feature_id', $trait_removing)->where('character_image_id', $character->image->id)->first();

        // Shouldn't be able to remove the trait if it's a born/origin trait Added traits will have the data of "Added from a consumable"
        // NOTE: Removed at Z's request
        // if ($matching_trait->data != "Added from a consumable") { throw new \Exception("Cannot remove a born trait"); }

        CharacterFeature::where('feature_id', $trait_removing)->delete();

        // Return the feature
        return $character;
    }

    private function actRerollAllTraits($character): void
    {
        // NOTE: This is how you get non born traits: $traits = CharacterFeature::where('character_image_id', $character->image->id)->where('data', 'Added from a consumable')->get();

        // Get all of a character's traits
        $traits = CharacterFeature::where('character_image_id', $character->image->id)->get();
        $traits_count = $traits->count();

        // Remove all the traits that were added from consumables
        CharacterFeature::where('character_image_id', $character->image->id)->delete();

        // Get a lsit of all traits that exist in the game
        $all_traits = Feature::all();

        // Add $traits_from_consumables_count new traits to the character but avoid adding the same trait twice
        $new_traits = CharacterManager::getRandomFeatures($traits_count, []);
        foreach ($new_traits as $trait)
        {
            CharacterFeature::create(['character_image_id' => $character->image->id, 'feature_id' => $trait->id, 'data' => 'Added from a consumable']);
        }
    }

    private function actRerollTrait($trait_rerolling_user, $character)
    {
        // Get all of a character's traits. Do this before removing so we can avoid rerolling into the same trait
        $character_traits = CharacterFeature::where('character_image_id', $character->image->id)->get();

        // Remove the old trait
        CharacterFeature::where('feature_id', $trait_rerolling_user)->where('character_image_id', $character->image->id)->delete();

        // Get a new trait to add to the character
        $new_trait = CharacterManager::getRandomFeatures(1, $character_traits);
        
        // Add the new trait to the character
        $feature = CharacterFeature::create(['character_image_id' => $character->image->id, 'feature_id' => $new_trait->id, 'data' => 'Added from a consumable']);
    }

    private function actRerollSpecies($character)
    {
        // Get the species that was added from a consumable
        $species_id = $character->image->species_id;

        // Get a list of all species that exist in the game except the current one
        $all_species = Species::where('id', '!=', $species_id)->get();

        // Get the new species to add to the character
        $new_species = $all_species->random();
        
        // Update the character's species
        $character->image->update(['species_id' => $new_species->id]);
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
