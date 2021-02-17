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
            if(!isset($data['name'])) throw new \Exception('Please name the stat');
            if(!isset($data['default'])) throw new \Exception('Please set a default.');
            if(!isset($data['abbreviation'])) throw new \Exception('Please add an abbreviation.');
            
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
            // Check first if the stat is currently owned or if some other site feature uses it
            if(DB::table('character_stats')->where('stat_id', $stat->id)->exists()) throw new \Exception("A character currently has this stat.");
           
            $stat->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}