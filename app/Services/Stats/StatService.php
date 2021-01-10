<?php namespace App\Services\Stats;

use App\Services\Service;

use DB;
use Config;

use App\Models\Stats\Character\Stat;
use App\Models\Item\Item;

class StatService extends Service
{
 /**
     * Creates a new stat.
     *
     */
    public function createStat($data)
    {
        DB::beginTransaction();

        try {

            $stat = Stat::create($data);

            return $this->commitReturn($stat);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates a stat.
     *
     */
    public function updateStat($stat, $data)
    {
        DB::beginTransaction();

        try {

            $stat->update($data);

            return $this->commitReturn($stat);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    /**
     * Deletes a stat.
     *
     */
    public function deleteStat($stat)
    {
        DB::beginTransaction();

        try {
            // NEED TO FINISH
            // Check first if the stat is currently owned or if some other site feature uses it
            if(DB::table('user_items')->where([['item_id', '=', $item->id], ['count', '>', 0]])->exists()) throw new \Exception("At least one user currently owns this item. Please remove the item(s) before deleting it.");
            if(DB::table('character_items')->where([['item_id', '=', $item->id], ['count', '>', 0]])->exists()) throw new \Exception("At least one character currently owns this item. Please remove the item(s) before deleting it.");
            if(DB::table('loots')->where('rewardable_type', 'Item')->where('rewardable_id', $item->id)->exists()) throw new \Exception("A loot table currently distributes this item as a potential reward. Please remove the item before deleting it.");
            if(DB::table('prompt_rewards')->where('rewardable_type', 'Item')->where('rewardable_id', $item->id)->exists()) throw new \Exception("A prompt currently distributes this item as a reward. Please remove the item before deleting it.");
            if(DB::table('shop_stock')->where('item_id', $item->id)->exists()) throw new \Exception("A shop currently stocks this item. Please remove the item before deleting it.");
            
            $stat->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}