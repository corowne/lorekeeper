<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use App\Models\ScavengerHunt\ScavengerHunt;
use App\Models\ScavengerHunt\HuntTarget;
use App\Models\ScavengerHunt\HuntParticipant;

class HuntService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Scavenger Hunt Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of scavenger hunts.
    |
    */

    /**
     * Creates a new scavenger hunt.
     *
     * @param  array                  $data 
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\ScavengerHunt\ScavengerHunt
     */
    public function createHunt($data, $user)
    {
        DB::beginTransaction();

        try {
            if(!$data['start_at']) throw new \Exception ('A start time is required.');
            if(!$data['end_at']) throw new \Exception ('An end time is required.');
            
            $hunt = ScavengerHunt::create(array_only($data, ['name', 'display_name', 'summary', 'clue', 'locations', 'is_active', 'start_at', 'end_at']));

            return $this->commitReturn($hunt);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates a scavenger hunt.
     *
     * @param  \App\Models\ScavengerHunt\ScavengerHunt  $hunt
     * @param  array                                    $data 
     * @param  \App\Models\User\User                    $user
     * @return bool|\App\Models\ScavengerHunt\ScavengerHunt
     */
    public function updateHunt($hunt, $data, $user)
    {
        DB::beginTransaction();

        try {
            if(ScavengerHunt::where('name', $data['name'])->where('id', '!=', $hunt->id)->exists()) throw new \Exception("The name has already been taken.");
            if(!$data['start_at']) throw new \Exception ('A start time is required.');
            if(!$data['end_at']) throw new \Exception ('An end time is required.');

            $hunt->update(array_only($data, ['name', 'display_name', 'summary', 'clue', 'locations', 'is_active', 'start_at', 'end_at']));

            return $this->commitReturn($hunt);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a scavenger hunt.
     *
     * @param  \App\Models\ScavengerHunt\ScavengerHunt  $hunt
     * @return bool
     */
    public function deleteHunt($hunt)
    {
        DB::beginTransaction();

        try {
            // Check first if the hunt has had participants
            if(HuntParticipant::where('hunt_id', $hunt->id)->exists()) throw new \Exception("A user has participated in this hunt, so deleting it would break the logs. While hunts remain visible after their end time, they cannot be interacted with.");

            $hunt->targets()->delete();
            $hunt->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**********************************************************************************************
     
        HUNT TARGETS

    **********************************************************************************************/
    
    /**
     * Creates a new target.
     *
     * @param  array                  $data 
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\ScavengerHunt\HuntTarget
     */
    public function createTarget($data, $user)
    {
        DB::beginTransaction();

        try {
            if(!$data['item_id']) throw new \Exception ('An item is required.');
            if(!$data['quantity']) throw new \Exception ('A quantity is required.');
            if(count(HuntTarget::where('hunt_id',$data['hunt_id'])->pluck('id')) == 10) throw new \Exception ('This hunt already has the maximum number of targets.');

            $pageId = randomString(10);
            if(HuntTarget::where('page_id', $pageId)->exists()) throw new \Exception("Failed to generate a unique page ID. Please try again!");
            
            $target = HuntTarget::create([
                'item_id' => $data['item_id'],
                'quantity' => $data['quantity'],
                'hunt_id' => $data['hunt_id'],
                'description' => isset($data['description']) ? $data['description'] : null,
                'page_id' => $pageId,
            ]);

            return $this->commitReturn($target);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates a scavenger hunt.
     *
     * @param  \App\Models\ScavengerHunt\HuntTarget     $target
     * @param  array                                    $data 
     * @param  \App\Models\User\User                    $user
     * @return bool|\App\Models\ScavengerHunt\HuntTarget
     */
    public function updateTarget($target, $data, $user)
    {
        DB::beginTransaction();

        try {
            if(!$data['item_id']) throw new \Exception ('An item is required.');
            if(!$data['quantity']) throw new \Exception ('A quantity is required.');

            $target->update([
                'item_id' => $data['item_id'],
                'quantity' => $data['quantity'],
                'description' => isset($data['description']) ? $data['description'] : null,
            ]);

            return $this->commitReturn($target);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a scavenger hunt.
     *
     * @param  \App\Models\ScavengerHunt\HuntTarget  $target
     * @return bool
     */
    public function deleteTarget($target)
    {
        DB::beginTransaction();

        try {
            // Check first if the hunt has had participants
            $hunt = ScavengerHunt::find($target->hunt_id);
            if(HuntParticipant::where('hunt_id', $hunt->id)->exists()) throw new \Exception("A user has participated in this hunt, so deleting one of its targets would break the logs. While hunts remain visible after their end time, they cannot be interacted with.");

            $target->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

}