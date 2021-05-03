<?php namespace App\Services;

use DB;
use Carbon\Carbon;
use App\Services\Service;

use App\Models\ScavengerHunt\ScavengerHunt;
use App\Models\ScavengerHunt\HuntTarget;
use App\Models\ScavengerHunt\HuntParticipant;
use App\Models\User\User;
use App\Models\Item\Item;

class HuntManager extends Service 
{
    /*
    |--------------------------------------------------------------------------
    | Scavenger Hunt Manager
    |--------------------------------------------------------------------------
    |
    | Handles user claiming of scavenger hunt targets.
    |
    */

    /**
     * Claims a scavenger hunt target.
     *
     * @param  array                 $data
     * @param  \App\Models\User\User $user
     * @return bool|App\Models\ScavengerHunt\HuntTarget
     */
    public function claimTarget($target, $user)
    {
        DB::beginTransaction();

        try {
            if(!$target) throw new \Exception ("Invalid target.");
            // Check that the target's parent hunt exists and is active.
            $hunt = $target->hunt;
            if(!$hunt) throw new \Exception ("Invalid hunt.");
            if(!$hunt->isActive) throw new \Exception ("This target\'s hunt isn\'t active.");

            // Log that the user found this particular target
            $participantLog = HuntParticipant::where([
                ['user_id', '=', $user->id],
                ['hunt_id', '=', $hunt->id],
            ])->first();
            if(isset($particpantLog) && isset($participantLog[$target->targetField])) throw new \Exception ('You have already claimed this target.'); 
            
            if(!$participantLog)
                $participantLog = HuntParticipant::create(['user_id' => $user->id, 'hunt_id' => $hunt->id]);
            $participantLog[$target->targetField] = Carbon::now();
            $participantLog->save();
            
            // Give the user the item(s)
            if(!(new InventoryManager)->creditItem(null, $user, 'Prize', [
                'data' => $participantLog->itemData, 
                'notes' => 'Claimed ' . format_date($participantLog[$target->targetField])
            ], $target->item, $target->quantity)) throw new \Exception("Failed to claim item.");

            return $this->commitReturn($target);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

}
