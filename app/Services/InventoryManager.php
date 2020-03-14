<?php namespace App\Services;

use Carbon\Carbon;
use App\Services\Service;

use DB;
use Config;
use Notifications;

use App\Models\User\User;
use App\Models\Item\Item;
use App\Models\User\UserItem;

class InventoryManager extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Inventory Manager
    |--------------------------------------------------------------------------
    |
    | Handles modification of user-owned items.
    |
    */

    /**
     * Grants an item to multiple users.
     *
     * @param  array                 $data
     * @param  \App\Models\User\User $staff
     * @return bool
     */
    public function grantItems($data, $staff)
    {
        DB::beginTransaction();

        try {
            if($data['quantity'] <= 0) throw new \Exception("The quantity must be at least 1.");

            // Process names
            $users = User::whereIn('name', explode(',', str_replace(' ', '', $data['names'])))->get();
            if(!count($users)) throw new \Exception("No valid users found.");

            // Process item
            $item = Item::find($data['item_id']);
            if(!$item) throw new \Exception("Invalid item selected.");

            foreach($users as $user) {
                $this->creditItem($staff, $user, 'Staff Grant', array_only($data, ['data', 'disallow_transfer', 'notes']), $item, $data['quantity']);
                Notifications::create('ITEM_GRANT', $user, [
                    'item_name' => $item->name,
                    'item_quantity' => $data['quantity'],
                    'sender_url' => $staff->url,
                    'sender_name' => $staff->name
                ]);
            }

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Transfers an item stack between users.
     *
     * @param  \App\Models\User\User      $sender
     * @param  \App\Models\User\User      $recipient
     * @param  \App\Models\User\UserItem  $stack
     * @return bool
     */
    public function transferStack($sender, $recipient, $stack)
    {
        DB::beginTransaction();

        try {
            if(!$sender->hasAlias) throw new \Exception("Your deviantART account must be verified before you can perform this action.");
            if(!$stack) throw new \Exception("Invalid item selected.");
            if($stack->user_id != $sender->id && !$sender->hasPower('edit_inventories')) throw new \Exception("You do not own this item.");
            if($stack->user_id == $recipient->id) throw new \Exception("Cannot send an item to the item's owner.");
            if(!$recipient) throw new \Exception("Invalid recipient selected.");
            if(!$recipient->hasAlias) throw new \Exception("Cannot transfer items to a non-verified member.");
            if($recipient->is_banned) throw new \Exception("Cannot transfer items to a banned member.");
            if((!$stack->item->allow_transfer || isset($stack->data['disallow_transfer'])) && !$sender->hasPower('edit_inventories')) throw new \Exception("This item cannot be transferred.");

            $oldUser = $stack->user;
            if($this->moveStack($stack->user, $recipient, ($stack->user_id == $sender->id ? 'User Transfer' : 'Staff Transfer'), ['data' => ($stack->user_id != $sender->id ? 'Transferred by '.$sender->displayName : '')], $stack)) 
            {
                Notifications::create('ITEM_TRANSFER', $recipient, [
                    'item_name' => $stack->item->name,
                    'item_quantity' => $data['quantity'],
                    'sender_url' => $sender->url,
                    'sender_name' => $sender->name
                ]);
                if($stack->user_id != $sender->id) 
                    Notifications::create('FORCED_ITEM_TRANSFER', $oldUser, [
                        'item_name' => $stack->item->name,
                        'item_quantity' => $data['quantity'],
                        'sender_url' => $sender->url,
                        'sender_name' => $sender->name
                    ]);
                return $this->commitReturn(true);
            }
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Deletes an item stack.
     *
     * @param  \App\Models\User\User      $user
     * @param  \App\Models\User\UserItem  $stack
     * @return bool
     */
    public function deleteStack($user, $stack)
    {
        DB::beginTransaction();

        try {
            if(!$user->hasAlias) throw new \Exception("Your deviantART account must be verified before you can perform this action.");
            if(!$stack) throw new \Exception("Invalid item selected.");
            if($stack->user_id != $user->id && !$user->hasPower('edit_inventories')) throw new \Exception("You do not own this item.");

            $oldUser = $stack->user;

            if($this->debitStack($stack->user, ($stack->user_id == $user->id ? 'User Deleted' : 'Staff Deleted'), ['data' => ($stack->user_id != $user->id ? 'Deleted by '.$user->displayName : '')], $stack)) 
            {
                if($stack->user_id != $user->id) 
                    Notifications::create('ITEM_REMOVAL', $oldUser, [
                        'item_name' => $stack->item->name,
                        'item_quantity' => 1,
                        'sender_url' => $user->url,
                        'sender_name' => $user->name
                    ]);
                return $this->commitReturn(true);
            }
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Credits an item to a user.
     *
     * @param  \App\Models\User\User  $sender
     * @param  \App\Models\User\User  $recipient
     * @param  string                 $type 
     * @param  array                  $data
     * @param  \App\Models\Item\Item  $item
     * @param  int                    $quantity
     * @return bool
     */
    public function creditItem($sender, $recipient, $type, $data, $item, $quantity)
    {
        DB::beginTransaction();

        try {
            for($i = 0; $i < $quantity; $i++) UserItem::create(['user_id' => $recipient->id, 'item_id' => $item->id, 'data' => json_encode($data)]);
            if($type && !$this->createLog($sender ? $sender->id : null, $recipient->id, null, $type, $data['data'], $item->id, $quantity)) throw new \Exception("Failed to create log.");

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Moves an item stack from one user to another.
     *
     * @param  \App\Models\User\User      $sender
     * @param  \App\Models\User\User      $recipient
     * @param  string                     $type 
     * @param  array                      $data
     * @param  \App\Models\User\UserItem  $item
     * @return bool
     */
    public function moveStack($sender, $recipient, $type, $data, $stack)
    {
        DB::beginTransaction();

        try {
            $stack->user_id = $recipient->id;
            $stack->save();

            if($type && !$this->createLog($sender ? $sender->id : null, $recipient->id, $stack->id, $type, $data['data'], $stack->item_id, 1)) throw new \Exception("Failed to create log.");

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Debits an item from a user.
     *
     * @param  \App\Models\User\User      $user
     * @param  string                     $type 
     * @param  array                      $data
     * @param  \App\Models\Item\UserItem  $stack
     * @return bool
     */
    public function debitStack($user, $type, $data, $stack)
    {
        DB::beginTransaction();

        try {
            $stack->delete();

            if($type && !$this->createLog($user ? $user->id : null, null, $stack->id, $type, $data['data'], $stack->item_id, 1)) throw new \Exception("Failed to create log.");

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    /**
     * Creates an inventory log.
     *
     * @param  int     $senderId
     * @param  int     $recipientId
     * @param  int     $stackId
     * @param  string  $type 
     * @param  string  $data
     * @param  int     $quantity
     * @return  int
     */
    public function createLog($senderId, $recipientId, $stackId, $type, $data, $itemId, $quantity)
    {
        return DB::table('user_items_log')->insert(
            [
                'sender_id' => $senderId,
                'recipient_id' => $recipientId,
                'stack_id' => $stackId,
                'log' => $type . ($data ? ' (' . $data . ')' : ''),
                'log_type' => $type,
                'data' => $data, // this should be just a string
                'item_id' => $itemId,
                'quantity' => $quantity,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        );
    }
}