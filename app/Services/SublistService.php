<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use App\Models\Character\Sublist;
use App\Models\Character\CharacterCategory;

class SublistService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Sub Masterlist Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of sub masterlists.
    |
    */

    /**********************************************************************************************
     
        SUB MASTERLISTS

    **********************************************************************************************/

    /**
     * Create a sublist.
     *
     * @param  array                 $data
     * @return \App\Models\Character\Sublist|bool
     */
    public function createSublist($data)
    {
        DB::beginTransaction();

        try {
            $sublist = Sublist::create($data);

            return $this->commitReturn($sublist);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Update a sublist.
     *
     * @param  \App\Models\Character\Sublist        $sublist
     * @param  array                                $data
     * @param  \App\Models\User\User                $user
     * @return \App\Models\Character\Sublist|bool
     */
    public function updateSublist($sublist, $data)
    {
        DB::beginTransaction();

        try {
            // More specific validation
            if(Sublist::where('name', $data['name'])->where('id', '!=', $sublist->id)->exists()) throw new \Exception("The name has already been taken.");

            $sublist->update($data);

            return $this->commitReturn($sublist);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Delete a sublist.
     *
     * @param  \App\Models\Character\Sublist  $sublist
     * @return bool
     */
    public function deleteSublist($sublist)
    {
        DB::beginTransaction();

        try {
            // Check first if the sublist is currently in use
            if(CharacterCategory::where('masterlist_sub_id', $sublist->id)->exists()) throw new \Exception("A character category is set to this sub masterlist. Please change its settings first.");
            
            $sublist->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}