<?php namespace App\Services\Item;

use App\Models\Character\CharacterFeature;
use App\Services\Service;
use Illuminate\Http\Request;

use DB;

use App\Services\InventoryManager;

use App\Models\Feature\Feature;

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
            'reroll_traits' => ['0' => 'Do not reroll traits', '1' => 'Reroll traits']
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
        $consumableData['reroll_traits'] = isset($tag->data['reroll_traits']) ? $tag->data['reroll_traits'] : null;

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
        $consumableData['reroll_traits'] = isset($data['reroll_traits']) ? $data['reroll_traits'] : null;

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
     * @param  \App\Models\User\CharacterItem  $stacks
     * @param  \App\Models\User\User      $user
     * @param  array                      $data
     * @return bool
     */
    public function act($stacks, $user, $data)
    {
        DB::beginTransaction();

        try {              
            foreach($stacks as $key=>$stack) {
                // We don't want to let anyone who isn't the owner of the Consumable to use it, so do some validation...
                if($user->characters()->where('id', $stack->character_id)->count() == 0) {
                    throw new \Exception("You do not own this consumable.");
                }

                $quantity = $data['quantities'][$key];

                $trait_adding = $stack->item->tag('consumable')->data['trait_added'];
                $trait_removing = $stack->item->tag('consumable')->data['trait_removed'];
                $reroll_traits = $stack->item->tag('consumable')->data['reroll_traits'];

                // NOTE: (Daire) If modifying traits, we only want to use one consumable at a time regardless of the quantity specified.
                if ($trait_adding != 0 || $trait_removing != 0 || $reroll_traits != 0)
                {
                    $quantity = "1";
                }

                // Next, try to delete the tag item. If successful, we can start distributing rewards.
                if((new InventoryManager)->debitStack($stack->character, 'Consumable Used', ['data' => ''], $stack, $quantity)) {
                    for($q=0; $q<$quantity; $q++) {
        
                        if ($trait_adding != 0)
                        {
                            $this->actAddTrait($trait_adding, $user, $data['character']);
                        }
                        else if ($trait_removing != 0)
                        {
                            $this->actRemoveTrait($trait_removing, $user, $data['character']);
                        }
                        else if ($reroll_traits != 0)
                        {
                            $this->actRerollTraits($reroll_traits, $user, $data['character']);
                        }
                        else
                        {
                            throw new \Exception("No action specified for Consumable.");
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

    private function actAddTrait($trait_adding, $user, $character_id)
    {
        // Check that the trait exists
        $trait = Feature::find($trait_adding);
        if (!$trait) { throw new \Exception("Trait not found."); }

        // Get the character to add the trait to
        $character = $user->characters()->where('id', $character_id)->first();

        // Check if the character already has the trait
        if ($character->image->features->contains('feature_id', $trait_adding)) { throw new \Exception("Character already has this trait."); }

        // Add the trait to the character
        $feature = CharacterFeature::create(['character_image_id' => $character->image->id, 'feature_id' => $trait_adding, 'data' => 'Added from a consumable']);

        // Return the feature
        return $feature;
    }

    private function actRemoveTrait($trait_removing, $user, $character_id)
    {
        // Check that the trait exists
        $trait = Feature::find($trait_removing);
        if (!$trait) { throw new \Exception("Trait not found."); }

        // Get the character to remove the trait from
        $character = $user->characters()->where('id', $character_id)->first();

        // Check if the character has the trait
        if (!$character->image->features->contains('feature_id', $trait_removing)) { throw new \Exception("Character does not have this trait."); }

        $matching_trait = CharacterFeature::where('feature_id', $trait_removing)->where('character_image_id', $character->image->id)->first();

        // Shouldn't be able to remove the trait if it's a born/origin trait Added traits will have the data of "Added from a consumable"
        if ($matching_trait->data != "Added from a consumable") { throw new \Exception("Cannot remove a born trait"); }

        CharacterFeature::where('feature_id', $trait_removing)->delete();

        // Return the feature
        return $character;
    }

    private function actRerollTraits($reroll_traits, $user, $character_id)
    {
        throw new \Exception("Rerolling traits is not yet implemented.");
    }
}
