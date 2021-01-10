<?php namespace App\Services\Stats;

use App\Services\Service;

use DB;
use Config;

use App\Models\Stats\User\Level;
use App\Models\Stats\Character\CharacterLevel;
use App\Models\Item\Item;

class LevelService extends Service
{
 /**
     * Creates a new level.
     *
     */
    public function createLevel($data)
    {
        DB::beginTransaction();

        try {

            $level = Level::create($data);

            return $this->commitReturn($level);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates a level.
     *
     */
    public function updateLevel($level, $data)
    {
        DB::beginTransaction();

        try {

            $level->update($data);

            return $this->commitReturn($level);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    /**
     * Deletes a level.
     *
     */
    public function deleteLevel($level)
    {
        DB::beginTransaction();

        try {
            // Check first if the level is currently owned or if some other site feature uses it
            if(DB::table('user_items')->where([['item_id', '=', $item->id], ['count', '>', 0]])->exists()) throw new \Exception("At least one user currently owns this item. Please remove the item(s) before deleting it.");
            if(DB::table('character_items')->where([['item_id', '=', $item->id], ['count', '>', 0]])->exists()) throw new \Exception("At least one character currently owns this item. Please remove the item(s) before deleting it.");
            if(DB::table('loots')->where('rewardable_type', 'Item')->where('rewardable_id', $item->id)->exists()) throw new \Exception("A loot table currently distributes this item as a potential reward. Please remove the item before deleting it.");
            if(DB::table('prompt_rewards')->where('rewardable_type', 'Item')->where('rewardable_id', $item->id)->exists()) throw new \Exception("A prompt currently distributes this item as a reward. Please remove the item before deleting it.");
            if(DB::table('shop_stock')->where('item_id', $item->id)->exists()) throw new \Exception("A shop currently stocks this item. Please remove the item before deleting it.");
            
            $level->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /*******************************************************************************
     * 
     *  CHARACTERS
     ******************************************************************************/
    /**
     * Creates a new level.
     *
     */
    public function createCharaLevel($data)
    {
        DB::beginTransaction();

        try {

            $level = CharacterLevel::create($data);

            return $this->commitReturn($level);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates a level.
     *
     */
    public function updateCharaLevel($level, $data)
    {
        DB::beginTransaction();

        try {

            $level->update($data);

            return $this->commitReturn($level);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    /**
     * Deletes a level.
     *
     */
    public function deleteCharaLevel($level)
    {
        DB::beginTransaction();

        try {
            // Check first if the level is currently owned or if some other site feature uses it
            if(DB::table('user_items')->where([['item_id', '=', $item->id], ['count', '>', 0]])->exists()) throw new \Exception("At least one user currently owns this item. Please remove the item(s) before deleting it.");
            if(DB::table('character_items')->where([['item_id', '=', $item->id], ['count', '>', 0]])->exists()) throw new \Exception("At least one character currently owns this item. Please remove the item(s) before deleting it.");
            if(DB::table('loots')->where('rewardable_type', 'Item')->where('rewardable_id', $item->id)->exists()) throw new \Exception("A loot table currently distributes this item as a potential reward. Please remove the item before deleting it.");
            if(DB::table('prompt_rewards')->where('rewardable_type', 'Item')->where('rewardable_id', $item->id)->exists()) throw new \Exception("A prompt currently distributes this item as a reward. Please remove the item before deleting it.");
            if(DB::table('shop_stock')->where('item_id', $item->id)->exists()) throw new \Exception("A shop currently stocks this item. Please remove the item before deleting it.");
            
            $level->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}