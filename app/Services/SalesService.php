<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use App\Models\User\User;
use App\Models\Sales;

class SalesService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Sales Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of Sales posts.
    |
    */

    /**
     * Creates a Sales post.
     *
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Sales
     */
    public function createSales($data, $user)
    {
        DB::beginTransaction();

        try {
            $data['parsed_text'] = parse($data['text']);
            $data['user_id'] = $user->id;
            if(!isset($data['is_visible'])) $data['is_visible'] = 0;
            if(!isset($data['is_open'])) $data['is_open'] = 0;

            $sales = Sales::create($data);

            if($sales->is_visible) $this->alertUsers();

            return $this->commitReturn($sales);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates a Sales post.
     *
     * @param  \App\Models\Sales       $Sales
     * @param  array                  $data 
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Sales
     */
    public function updateSales($sales, $data, $user)
    {
        DB::beginTransaction();

        try {
            $data['parsed_text'] = parse($data['text']);
            $data['user_id'] = $user->id;
            if(!isset($data['is_visible'])) $data['is_visible'] = 0;
            if(!isset($data['is_open'])) $data['is_open'] = 0;

            if(isset($data['bump']) && $data['is_visible'] == 1 && $data['bump'] == 1) $this->alertUsers();

            $sales->update($data);

            return $this->commitReturn($sales);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a Sales post.
     *
     * @param  \App\Models\Sales  $Sales
     * @return bool
     */
    public function deleteSales($sales)
    {
        DB::beginTransaction();

        try {
            $sales->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates queued Sales posts to be visible and alert users when
     * they should be posted.
     *
     * @return bool
     */
    public function updateQueue()
    {
        $count = Sales::shouldBeVisible()->count();
        if($count) {
            DB::beginTransaction();

            try {
                Sales::shouldBeVisible()->update(['is_visible' => 1]);
                $this->alertUsers();

                return $this->commitReturn(true);
            } catch(\Exception $e) { 
                $this->setError('error', $e->getMessage());
            }
            return $this->rollbackReturn(false);
        }
    }

    /**
     * Updates the unread Sales flag for all users so that
     * the new Sales notification is displayed.
     *
     * @return bool
     */
    private function alertUsers()
    {
        User::query()->update(['is_sales_unread' => 1]);
        return true;
    }
}