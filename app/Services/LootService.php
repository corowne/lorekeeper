<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use App\Models\Loot\LootTable;
use App\Models\Loot\Loot;

class LootService extends Service
{
    public function createLootTable($data)
    {
        DB::beginTransaction();

        try {
            
            // More specific validation
            foreach($data['rewardable_type'] as $key => $type)
            {
                if(!$type) throw new \Exception("Loot type is required.");
                if(!$data['rewardable_id'][$key]) throw new \Exception("Reward is required.");
                if(!$data['quantity'][$key] || $data['quantity'][$key] < 1) throw new \Exception("Quantity is required and must be an integer greater than 0.");
                if(!$data['weight'][$key] || $data['weight'][$key] < 1) throw new \Exception("Weight is required and must be an integer greater than 0.");
            }

            $table = LootTable::create(array_only($data, ['name', 'display_name']));

            $this->populateLootTable($table, array_only($data, ['rewardable_type', 'rewardable_id', 'quantity', 'weight']));

            return $this->commitReturn($table);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    public function updateLootTable($table, $data)
    {
        DB::beginTransaction();

        try {
            
            // More specific validation
            foreach($data['rewardable_type'] as $key => $type)
            {
                if(!$type) throw new \Exception("Loot type is required.");
                if(!$data['rewardable_id'][$key]) throw new \Exception("Reward is required.");
                if(!$data['quantity'][$key] || $data['quantity'][$key] < 1) throw new \Exception("Quantity is required and must be an integer greater than 0.");
                if(!$data['weight'][$key] || $data['weight'][$key] < 1) throw new \Exception("Weight is required and must be an integer greater than 0.");
            }

            $table->update(array_only($data, ['name', 'display_name']));

            $this->populateLootTable($table, array_only($data, ['rewardable_type', 'rewardable_id', 'quantity', 'weight']));

            return $this->commitReturn($table);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    private function populateLootTable($table, $data)
    {
        // Clear the old loot...
        $table->loot()->delete();

        foreach($data['rewardable_type'] as $key => $type)
        {
            Loot::create([
                'loot_table_id'   => $table->id,
                'rewardable_type' => $type,
                'rewardable_id'   => $data['rewardable_id'][$key],
                'quantity'        => $data['quantity'][$key],
                'weight'          => $data['weight'][$key]
            ]);
        }
    }
    
    public function deleteLootTable($table)
    {
        DB::beginTransaction();

        try {
            // Check first if the table is currently in use
            // - Prompts
            
            $table->loot()->delete();
            $table->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}