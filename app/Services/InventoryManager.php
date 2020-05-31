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
            foreach($data['quantities'] as $q) {
                if($q <= 0) throw new \Exception("All quantities must be at least 1.");
            }

            // Process names
            $users = User::find($data['names']);
            if(count($users) != count($data['names'])) throw new \Exception("An invalid user was selected.");

            $keyed_quantities = [];
            array_walk($data['item_ids'], function($id, $key) use(&$keyed_quantities, $data) {
                if($id != null && !in_array($id, array_keys($keyed_quantities), TRUE)) {
                    $keyed_quantities[$id] = $data['quantities'][$key];
                }
            });

            // Process item
            $items = Item::find($data['item_ids']);
            if(!count($items)) throw new \Exception("No valid items found.");

            foreach($users as $user) {
                foreach($items as $item) {
                    if($this->creditItem($staff, $user, 'Staff Grant', array_only($data, ['data', 'disallow_transfer', 'notes']), $item, $keyed_quantities[$item->id]))
                    {
                        Notifications::create('ITEM_GRANT', $user, [
                            'item_name' => $item->name,
                            'item_quantity' => $keyed_quantities[$item->id],
                            'sender_url' => $staff->url,
                            'sender_name' => $staff->name
                        ]);
                    }
                    else
                    {
                        throw new \Exception("Failed to credit items to ".$user->name.".");
                    }
                }
            }
            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Transfers items between user stacks.
     *
     * @param  \App\Models\User\User      $sender
     * @param  \App\Models\User\User      $recipient
     * @param  \App\Models\User\UserItem  $stacks
     * @param  int                        $quantities
     * @return bool
     */
    public function transferStack($sender, $recipient, $stacks, $quantities)
    {
        DB::beginTransaction();

        try {
            foreach($stacks as $key=>$stack) {
                $quantity = $quantities[$key];
                if(!$sender->hasAlias) throw new \Exception("Your deviantART account must be verified before you can perform this action.");
                if(!$stack) throw new \Exception("An invalid item was selected.");
                if($stack->user_id != $sender->id && !$sender->hasPower('edit_inventories')) throw new \Exception("You do not own one of the selected items.");
                if($stack->user_id == $recipient->id) throw new \Exception("Cannot send items to the item's owner.");
                if(!$recipient) throw new \Exception("Invalid recipient selected.");
                if(!$recipient->hasAlias) throw new \Exception("Cannot transfer items to a non-verified member.");
                if($recipient->is_banned) throw new \Exception("Cannot transfer items to a banned member.");
                if((!$stack->item->allow_transfer || isset($stack->data['disallow_transfer'])) && !$sender->hasPower('edit_inventories')) throw new \Exception("One of the selected items cannot be transferred.");
                if($stack->count < $quantity) throw new \Exception("Quantity to transfer exceeds item count.");

                $oldUser = $stack->user;
                if($this->moveStack($stack->user, $recipient, ($stack->user_id == $sender->id ? 'User Transfer' : 'Staff Transfer'), ['data' => ($stack->user_id != $sender->id ? 'Transferred by '.$sender->displayName : '')], $stack, $quantity)) 
                {
                    Notifications::create('ITEM_TRANSFER', $recipient, [
                        'item_name' => $stack->item->name,
                        'item_quantity' => $quantity,
                        'sender_url' => $sender->url,
                        'sender_name' => $sender->name
                    ]);
                    if($stack->user_id != $sender->id) 
                        Notifications::create('FORCED_ITEM_TRANSFER', $oldUser, [
                            'item_name' => $stack->item->name,
                            'item_quantity' => $quantity,
                            'sender_url' => $sender->url,
                            'sender_name' => $sender->name
                        ]);
                }
            }
            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Deletes items from stack.
     *
     * @param  \App\Models\User\User      $user
     * @param  \App\Models\User\UserItem  $stacks
     * @param  int                        $quantities
     * @return bool
     */
    public function deleteStack($user, $stacks, $quantities)
    {
        DB::beginTransaction();

        try {
            foreach($stacks as $key=>$stack) {
                $quantity = $quantities[$key];
                if(!$user->hasAlias) throw new \Exception("Your deviantART account must be verified before you can perform this action.");
                if(!$stack) throw new \Exception("An invalid item was selected.");
                if($stack->user_id != $user->id && !$user->hasPower('edit_inventories')) throw new \Exception("You do not own one of the selected items.");
                if($stack->count < $quantity) throw new \Exception("Quantity to delete exceeds item count.");
                
                $oldUser = $stack->user;

                if($this->debitStack($stack->user, ($stack->user_id == $user->id ? 'User Deleted' : 'Staff Deleted'), ['data' => ($stack->user_id != $user->id ? 'Deleted by '.$user->displayName : '')], $stack, $quantity)) 
                {
                    if($stack->user_id != $user->id) 
                        Notifications::create('ITEM_REMOVAL', $oldUser, [
                            'item_name' => $stack->item->name,
                            'item_quantity' => $quantity,
                            'sender_url' => $user->url,
                            'sender_name' => $user->name
                        ]);
                }
            }
            return $this->commitReturn(true);
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
            $encoded_data = \json_encode($data);

            $recipient_stack = UserItem::where([
                ['user_id', '=', $recipient->id],
                ['item_id', '=', $item->id],
                ['data', '=', $encoded_data]
            ])->first();
            
            if(!$recipient_stack)
                $recipient_stack = UserItem::create(['user_id' => $recipient->id, 'item_id' => $item->id, 'data' => $encoded_data]);
            $recipient_stack->count += $quantity;
            $recipient_stack->save();
            
            if($type && !$this->createLog($sender ? $sender->id : null, $recipient->id, null, $type, $data['data'], $item->id, $quantity)) throw new \Exception("Failed to create log.");

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Moves items from one user stack to another.
     *
     * @param  \App\Models\User\User      $sender
     * @param  \App\Models\User\User      $recipient
     * @param  string                     $type 
     * @param  array                      $data
     * @param  \App\Models\User\UserItem  $item
     * @return bool
     */
    public function moveStack($sender, $recipient, $type, $data, $stack, $quantity)
    {
        DB::beginTransaction();

        try {
            $recipient_stack = UserItem::where([
                ['user_id', '=', $recipient->id],
                ['item_id', '=', $stack->item_id],
                ['data', '=', json_encode($stack->data)]
            ])->first();

            if(!$recipient_stack)
                $recipient_stack = UserItem::create(['user_id' => $recipient->id, 'item_id' => $stack->item_id, 'data' => json_encode($stack->data)]);
                
            $stack->count -= $quantity;
            $recipient_stack->count += $quantity;
            $stack->save();
            $recipient_stack->save();

            if($type && !$this->createLog($sender ? $sender->id : null, $recipient->id, $stack->id, $type, $data['data'], $stack->item_id, $quantity)) throw new \Exception("Failed to create log.");

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
    public function debitStack($user, $type, $data, $stack, $quantity)
    {
        DB::beginTransaction();

        try {
            $stack->count -= $quantity;
            $stack->save();

            if($type && !$this->createLog($user ? $user->id : null, null, $stack->id, $type, $data['data'], $stack->item_id, $quantity)) throw new \Exception("Failed to create log.");

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