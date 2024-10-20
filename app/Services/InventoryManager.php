<?php namespace App\Services;

use Carbon\Carbon;
use App\Services\Service;

use DB;
use Auth;
use Config;
use Notifications;

use Illuminate\Support\Arr;
use App\Models\User\User;
use App\Models\Item\Item;
use App\Models\Item\ItemCategory;
use App\Models\User\UserItem;
use App\Models\Character\CharacterItem;
use App\Models\Currency\Currency;
use App\Models\Shop\UserShopStock;

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
                    if($this->creditItem($staff, $user, 'Staff Grant', Arr::only($data, ['data', 'disallow_transfer', 'notes']), $item, $keyed_quantities[$item->id]))
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
     * Grants an item to a character.
     *
     * @param  array                            $data
     * @param  \App\Models\Character\Character  $character
     * @param  \App\Models\User\User            $staff
     * @return bool
     */
    public function grantCharacterItems($data, $character, $staff)
    {
        DB::beginTransaction();

        try {
            if(!$character) throw new \Exception("Invalid character selected.");

            foreach($data['quantities'] as $q) {
                if($q <= 0) throw new \Exception("All quantities must be at least 1.");
            }

            $keyed_quantities = [];
            array_walk($data['item_ids'], function($id, $key) use(&$keyed_quantities, $data) {
                if($id != null && !in_array($id, array_keys($keyed_quantities), TRUE)) {
                    $keyed_quantities[$id] = $data['quantities'][$key];
                }
            });

            // Process item(s)
            $items = Item::find($data['item_ids']);
            foreach($items as $i) {
                if(!$i->category->is_character_owned) throw new \Exception("One of these items cannot be owned by characters.");
            }
            if(!count($items)) throw new \Exception("No valid items found.");
            
            foreach($items as $item) {
                $this->creditItem($staff, $character, 'Staff Grant', Arr::only($data, ['data', 'disallow_transfer', 'notes']), $item, $keyed_quantities[$item->id]);
                if($character->is_visible && $character->user_id) {
                    Notifications::create('CHARACTER_ITEM_GRANT', $character->user, [
                        'item_name' => $item->name,
                        'item_quantity' => $keyed_quantities[$item->id],
                        'sender_url' => $staff->url,
                        'sender_name' => $staff->name,
                        'character_name' => $character->fullName,
                        'character_slug' => $character->slug,
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
     * Transfers items between a user and character.
     *
     * @param  \App\Models\User\User|\App\Models\Character\Character          $sender
     * @param  \App\Models\User\User|\App\Models\Character\Character          $recipient
     * @param  \App\Models\User\UserItem|\App\Models\Character\CharacterItem  $stacks
     * @param  int                                                            $quantities
     * @return bool
     */
    public function transferCharacterStack($sender, $recipient, $stacks, $quantities)
    {
        DB::beginTransaction();

        try {
            foreach($stacks as $key=>$stack) {
                $quantity = $quantities[$key];

                if(!$stack) throw new \Exception("Invalid or no stack selected.");
                if(!$recipient) throw new \Exception("Invalid recipient selected.");
                if(!$sender) throw new \Exception("Invalid sender selected.");

                if($recipient->logType == 'Character' && $sender->logType == 'Character') throw new \Exception("Cannot transfer items between characters.");
                if($recipient->logType == 'Character' && !$sender->hasPower('edit_inventories') && !$recipient->is_visible) throw new \Exception("Invalid character selected.");
                if(!$stacks) throw new \Exception("Invalid stack selected.");
                if($sender->logType == 'Character' && $quantity <= 0 && $stack->count > 0) $quantity = $stack->count;
                if($quantity <= 0) throw new \Exception("Invalid quantity entered.");
                
                if(($recipient->logType == 'Character' && !$sender->hasPower('edit_inventories') && !Auth::user() == $recipient->user) || ($recipient->logType == 'User' && !Auth::user()->hasPower('edit_inventories') && !Auth::user() == $sender->user)) throw new \Exception("Cannot transfer items to/from a character you don't own.");
                
                if($recipient->logType == 'Character' && !$stack->item->category->is_character_owned) throw new \Exception("One of the selected items cannot be owned by characters.");
                if((!$stack->item->allow_transfer || isset($stack->data['disallow_transfer'])) && !Auth::user()->hasPower('edit_inventories')) throw new \Exception("One of the selected items cannot be transferred.");
                if($stack->count < $quantity) throw new \Exception("Quantity to transfer exceeds item count.");

                //Check that hold count isn't being exceeded
                if($stack->item->category->character_limit > 0) $limit = $stack->item->category->character_limit;
                if($recipient->logType == 'Character' && isset($limit)) {
                    $limitedItems = Item::where('item_category_id', $stack->item->category->id);
                    $ownedLimitedItems = CharacterItem::with('item')->whereIn('item_id', $limitedItems->pluck('id'))->whereNull('deleted_at')->where('count', '>', '0')->where('character_id', $recipient->id)->get();
                    $newOwnedLimit = $ownedLimitedItems->pluck('count')->sum() + $quantity;
                }

                if($recipient->logType == 'Character' && isset($limit) && ($ownedLimitedItems->pluck('count')->sum() >= $limit || $newOwnedLimit > $limit)) throw new \Exception("One of the selected items exceeds the limit characters can own for its category.");

                $this->creditItem($sender, $recipient, $sender->logType == 'User' ? 'User → Character Transfer' : 'Character → User Transfer', $stack->data, $stack->item, $quantity);

                $stack->count -= $quantity;
                $stack->save();
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
                if(!$sender->hasAlias) throw new \Exception("You need to have a linked social media account before you can perform this action.");
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
     * @param  \App\Models\User\User|\App\Models\Character\Character          $owner
     * @param  \App\Models\User\UserItem|\App\Models\Character\CharacterItem  $stacks
     * @param  int                                                            $quantities
     * @return bool
     */
    public function deleteStack($owner, $stacks, $quantities)
    {
        DB::beginTransaction();

        try {
            if($owner->logType == 'User') {
                foreach($stacks as $key=>$stack) {
                    $user = Auth::user();
                    $quantity = $quantities[$key];
                    if(!$owner->hasAlias) throw new \Exception("You need to have a linked social media account before you can perform this action.");
                    if(!$stack) throw new \Exception("An invalid item was selected.");
                    if($stack->user_id != $owner->id && !$user->hasPower('edit_inventories')) throw new \Exception("You do not own one of the selected items.");
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
            }
            else {
                foreach($stacks as $key=>$stack) {
                    $quantity = $quantities[$key];
                    $user = Auth::user();
                    if(!$user->hasAlias) throw new \Exception("You need to have a linked social media account before you can perform this action.");
                    if(!$stack) throw new \Exception("An invalid item was selected.");
                    if($stack->character->user_id != $user->id && !$user->hasPower('edit_inventories')) throw new \Exception("You do not own one of the selected items.");
                    if($stack->count < $quantity) throw new \Exception("Quantity to delete exceeds item count.");

                    if($this->debitStack($stack->character, ($stack->character->user_id == $user->id ? 'User Deleted' : 'Staff Deleted'), ['data' => ($stack->character->user_id != $user->id ? 'Deleted by '.$user->displayName : '')], $stack, $quantity)) 
                    {
                        if($stack->character->user_id != $user->id && $stack->character->is_visible && $stack->character->user_id)
                            Notifications::create('CHARACTER_ITEM_REMOVAL', $stack->character->user, [
                                'item_name' => $stack->item->name,
                                'item_quantity' => $quantity,
                                'sender_url' => $user->url,
                                'sender_name' => $user->name,
                                'character_name' => $stack->character->fullName,
                                'character_slug' => $stack->character->slug
                            ]);
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
     * Sells items from stack.
     *
     * @param  \App\Models\User\User      $user
     * @param  \App\Models\User\UserItem  $stacks
     * @param  int                        $quantities
     * @return bool
     */
    public function resellStack($user, $stacks, $quantities)
    {
        DB::beginTransaction();

        try {
            foreach($stacks as $key=>$stack) {
                $quantity = $quantities[$key];
                if(!$user->hasAlias) throw new \Exception("You need to have a linked social media account before you can perform this action.");
                if(!$stack) throw new \Exception("An invalid item was selected.");
                if($stack->user_id != $user->id && !$user->hasPower('edit_inventories')) throw new \Exception("You do not own one of the selected items.");
                if($stack->count < $quantity) throw new \Exception("Quantity to sell exceeds item count.");
                if(!isset($stack->item->data['resell'])) throw new \Exception ("This item cannot be sold.");
                if(!Config::get('lorekeeper.extensions.item_entry_expansion.resale_function')) throw new \Exception("This function is not currently enabled.");
                
                $oldUser = $stack->user;

                $currencyManager = new CurrencyManager;
                if(isset($stack->item->data['resell']) && $stack->item->data['resell'])
                {
                    $currency = $stack->item->resell->flip()->pop();
                    $currencyQuantity = $stack->item->resell->pop() * $quantity;

                    if(!$currencyManager->creditCurrency(null, $oldUser, 'Sold Item', 'Sold '.$stack->item->displayName.' ×'.$quantity, $currency, $currencyQuantity)) 
                            throw new \Exception("Failed to credit currency.");
                }

                if($this->debitStack($stack->user, ($stack->user_id == $user->id ? 'Sold by User' : 'Sold by Staff'), ['data' => ($stack->user_id != $user->id ? 'Sold by '.$user->displayName : '')], $stack, $quantity)) 
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
     * Credits an item to a user or character.
     *
     * @param  \App\Models\User\User|\App\Models\Character\Character  $sender
     * @param  \App\Models\User\User|\App\Models\Character\Character  $recipient
     * @param  string                                                 $type 
     * @param  array                                                  $data
     * @param  \App\Models\Item\Item                                  $item
     * @param  int                                                    $quantity
     * @return bool
     */
    public function creditItem($sender, $recipient, $type, $data, $item, $quantity)
    {
        DB::beginTransaction();

        try {
            $encoded_data = \json_encode($data);

            if($recipient->logType == 'User') {
                $recipient_stack = UserItem::where([
                    ['user_id', '=', $recipient->id],
                    ['item_id', '=', $item->id],
                    ['data', '=', $encoded_data]
                ])->first();
                
                if(!$recipient_stack)
                    $recipient_stack = UserItem::create(['user_id' => $recipient->id, 'item_id' => $item->id, 'data' => $encoded_data]);
                $recipient_stack->count += $quantity;
                $recipient_stack->save();
            }
            else {
                $recipient_stack = CharacterItem::where([
                    ['character_id', '=', $recipient->id],
                    ['item_id', '=', $item->id],
                    ['data', '=', $encoded_data]
                ])->first();
                
                if(!$recipient_stack)
                    $recipient_stack = CharacterItem::create(['character_id' => $recipient->id, 'item_id' => $item->id, 'data' => $encoded_data]);
                $recipient_stack->count += $quantity;
                $recipient_stack->save();
            }
            if($type && !$this->createLog($sender ? $sender->id : null, $sender ? $sender->logType : null, $recipient ? $recipient->id : null, $recipient ? $recipient->logType : null, null, $type, $data['data'], $item->id, $quantity)) throw new \Exception("Failed to create log.");

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Moves items from one user or character stack to another.
     *
     * @param  \App\Models\User\User|\App\Models\Character\Character          $sender
     * @param  \App\Models\User\User|\App\Models\Character\Character          $recipient
     * @param  string                                                         $type 
     * @param  array                                                          $data
     * @param  \App\Models\User\UserItem|\App\Models\Character\CharacterItem  $item
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

            if($type && !$this->createLog($sender ? $sender->id : null, $sender ? $sender->logType : null, $recipient->id, $recipient ? $recipient->logType : null, $stack->id, $type, $data['data'], $stack->item_id, $quantity)) throw new \Exception("Failed to create log.");

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Debits an item from a user or character.
     *
     * @param  \App\Models\User\User|\App\Models\Character\Character  $owner
     * @param  string                                                 $type 
     * @param  array                                                  $data
     * @param  \App\Models\Item\UserItem                              $stack
     * @return bool
     */
    public function debitStack($owner, $type, $data, $stack, $quantity)
    {
        DB::beginTransaction();

        try {
            $stack->count -= $quantity;
            $stack->save();

            if($type && !$this->createLog($owner ? $owner->id : null, $owner ? $owner->logType : null, null, null, $stack->id, $type, $data['data'], $stack->item->id, $quantity)) throw new \Exception("Failed to create log."); 

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Names an item stack.
     *
     * @param  \App\Models\User\User|\App\Models\Character\Character          $owner
     * @param  \App\Models\User\UserItem|\App\Models\Character\CharacterItem  $stacks
     * @param  int                                                            $quantities
     * @return bool
     */
    public function nameStack($owner, $stacks, $name)
    {
        DB::beginTransaction();

        try {
            foreach($stacks as $key=>$stack) {
                $user = Auth::user();
                if(!$user->hasAlias) throw new \Exception("You need to have a linked social media account before you can perform this action.");
                if(!$stack) throw new \Exception("An invalid item was selected.");
                if($stack->character->user_id != $user->id && !$user->hasPower('edit_inventories')) throw new \Exception("You do not own one of the selected items.");

                $stack['stack_name'] = $name;
                $stack->save();
            }
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
     * @param  string  $senderType
     * @param  int     $recipientId
     * @param  string  $recipientType
     * @param  int     $stackId
     * @param  string  $type 
     * @param  string  $data
     * @param  int     $quantity
     * @return  int
     */
    public function createLog($senderId, $senderType, $recipientId, $recipientType, $stackId, $type, $data, $itemId, $quantity)
    {
        
        return DB::table('items_log')->insert(
            [
                'sender_id' => $senderId,
                'sender_type' => $senderType,
                'recipient_id' => $recipientId,
                'recipient_type' => $recipientType,
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


    /**
     * quickstocks items between a user and shop.
     *
     * @param  \App\Models\User\User|\App\Models\Shop\UserShop          $sender
     * @param  \App\Models\User\User|\App\Models\Shop\UserShop          $recipient
     * @param  \App\Models\User\UserItem|\App\Models\Shop\UserShopStock  $stacks
     * @param  int                                                            $quantities
     * @return bool
     */
    public function sendShop($sender, $recipient, $stack, $quantity)
    {
        DB::beginTransaction();

        try {
                if(!$stack) throw new \Exception("Invalid or no stack selected.");
                if(!$recipient) throw new \Exception("Invalid recipient selected.");
                if(!$sender) throw new \Exception("Invalid sender selected.");

                if($recipient->logType == 'Shop' && $sender->logType == 'Shop') throw new \Exception("Cannot transfer items between shops.");
                if($sender->logType == 'Shop' && $quantity <= 0 && $stack->count > 0) $quantity = $stack->count;
                if($quantity <= 0) throw new \Exception("Invalid quantity entered.");
                
                if(($recipient->logType == 'Shop' && !$sender->hasPower('edit_inventories') && !Auth::user() == $recipient->user) || ($recipient->logType == 'User' && !Auth::user()->hasPower('edit_inventories') && !Auth::user() == $sender->user)) throw new \Exception("Cannot transfer items to/from a shop you don't own.");
                
                //streamlining and also adding a small failsafe in case transfer status gets changed to unsellable for any reason while an item is stocked
                //items won't get trapped this way
                if($recipient->logType == 'Shop' && !$stack->isTransferrable && !Auth::user()->hasPower('edit_inventories')) throw new \Exception("One of the selected items cannot be transferred.");
                if($recipient->logType == 'Shop' && !$stack->item->canUserSell) throw new \Exception("This item cannot be sold in user shops."); 
                
                if($recipient->logType == 'Shop' && $stack->count < $quantity) throw new \Exception("Quantity to transfer exceeds item count."); 

                if($recipient->logType == 'User' && $stack->quantity < $quantity) throw new \Exception("Quantity to transfer exceeds item count."); 

                if(!$this->shopItem($sender, $recipient, $sender->logType == 'User' ? 'User → Shop Transfer' : 'Shop → User Transfer', $stack->data, $stack->item, $quantity)) throw new \Exception("Could not transfer item to shop.");
                
                if($sender->logType == 'Shop'){
                    $stack->quantity -= $quantity;
                    $stack->save();
                    if($stack->quantity == 0) $stack->delete(); 
                }
                else{
                    $stack->count -= $quantity;
                    $stack->save();
                }

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Credits an item to a user or shop.
     *
     * @param  \App\Models\User\User|\App\Models\Shop\UserShop  $sender
     * @param  \App\Models\User\User|\App\Models\Shop\UserShop  $recipient
     * @param  string                                                 $type 
     * @param  array                                                  $data
     * @param  \App\Models\Item\Item                                  $item
     * @param  int                                                    $quantity
     * @return bool
     */
    public function shopItem($sender, $recipient, $type, $data, $item, $quantity)
    {
        DB::beginTransaction();

        try {
            $data = ['data' => '', 'notes' => '']; //make back and forth data blank for both transfers because things get. wacky. 
            $encoded_data = \json_encode($data); 

            if($recipient->logType == 'User') {
                $recipient_stack = UserItem::where([
                    ['user_id', '=', $recipient->id],
                    ['item_id', '=', $item->id],
                    ['data', '=', $encoded_data]
                ])->first();
                
                if(!$recipient_stack)
                    $recipient_stack = UserItem::create(['user_id' => $recipient->id, 'item_id' => $item->id, 'data' => $encoded_data]);
                $recipient_stack->count += $quantity;
                $recipient_stack->save();
            }
            else {
                $recipient_stack = UserShopStock::where([
                    ['user_shop_id', '=', $recipient->id],
                    ['item_id', '=', $item->id],
                    ['data', '=', $encoded_data]
                ])->first();
                
                if(!$recipient_stack)
                    $recipient_stack = UserShopStock::create(['user_shop_id' => $recipient->id, 'item_id' => $item->id, 'data' => $encoded_data]);
                $recipient_stack->quantity += $quantity;
                $recipient_stack->save();
            }
            if($type && !$this->createLog($sender ? $sender->id : null, $sender ? $sender->logType : null, $recipient ? $recipient->id : null, $recipient ? $recipient->logType : null, null, $type, $data['data'], $item->id, $quantity)) throw new \Exception("Failed to create log.");
            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * quickstock items
     *
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @param  bool                   $isClaim
     * @return mixed
     */
    public function quickstockItems($data, $user, $recipient)
    {
        DB::beginTransaction();

        try {

            if(isset($data['stack_id'])) {
                foreach($data['stack_id'] as $stackId) {
                    $stack = UserItem::with('item')->find($stackId);
                    if(!$stack || $stack->user_id != $user->id) throw new \Exception("Invalid item selected.");
                    if(!isset($data['stack_quantity'][$stackId])) throw new \Exception("Invalid quantity selected.");
                    if(!$this->sendShop($user, $recipient, $stack, $data['stack_quantity'][$stackId])) throw new \Exception("Could not transfer item to shop.");
                }
            }

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
}