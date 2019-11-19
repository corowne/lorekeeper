<?php namespace App\Services;

use App\Services\Service;

use Carbon\Carbon;

use DB;
use Config;
use Image;
use Notifications;
use Settings;

use App\Models\User\User;
use App\Models\User\UserItem;
use App\Models\Character\Character;
use App\Models\Submission\Submission;
use App\Models\Submission\SubmissionCharacter;
use App\Models\Currency\Currency;
use App\Models\Item\Item;
use App\Models\Loot\LootTable;
use App\Models\Prompt\Prompt;
use App\Models\Trade;

class TradeManager extends Service
{
    public function createTrade($data, $user)
    {
        DB::beginTransaction();

        try {
            if(!isset($data['recipient_id'])) throw new \Exception("Please select a recipient.");
            $recipient = User::find($data['recipient_id']);
            if($recipient->is_banned) throw new \Exception("The recipient is a banned user and cannot receive a trade.");

            $trade = Trade::create([
                'sender_id' => $user->id,
                'recipient_id' => $data['recipient_id'],
                'status' => 'Open',
                'comments' => isset($data['comments']) ? $data['comments'] : null,
                'is_sender_confirmed' => 0,
                'is_recipient_confirmed' => 0,
                'data' => null
            ]);

            if($assetData = $this->handleTradeAssets($trade, $data, $user)) {
                
                $trade->data = json_encode(['sender' => getDataReadyAssets($assetData['sender'])]);
                $trade->save();

                // send a notification
                Notifications::create('TRADE_RECEIVED', $recipient, [
                    'sender_url' => $sender->url,
                    'sender_name' => $sender->name,
                    'trade_id' => $trade->id
                ]);

                return $this->commitReturn($trade);
            }

        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    public function editTrade($data, $user)
    {
        DB::beginTransaction();
        try {
            if(!isset($data['trade'])) $trade = Trade::where('status', 'Open')->where('id', $data['id'])->where(function($query) use ($user) {
                $query->where('sender_id', $user->id)->orWhere('recipient_id', $user->id);
            })->first();
            elseif($data['trade']->status == 'Open') $trade = $data['trade'];
            else $trade = null;
            if(!$trade) throw new \Exception("Invalid trade.");

            if($assetData = $this->handleTradeAssets($trade, $data, $user)) {
                $tradeData = $trade->data;
                $isSender = ($trade->sender_id == $user->id);
                $tradeData[$isSender ? 'sender' : 'recipient'] = getDataReadyAssets($assetData[$isSender ? 'sender' : 'recipient']);
                $trade->data = json_encode($tradeData);
                $trade->{'is_'.($isSender ? 'sender' : 'recipient').'_confirmed'} = 0;
                $trade->{'is_'.($isSender ? 'recipient' : 'sender').'_trade_confirmed'} = 0;
                $trade->save();
                return $this->commitReturn($trade);
            }
            
            return $this->commitReturn($trade);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    private function handleTradeAssets($trade, $data, $user)
    {
        DB::beginTransaction();
        try {
            // First return any item stacks attached to the trade
            DB::table('user_items')->where('user_id', $user->id)->where('holding_type', 'Trade')->where('holding_id', $trade->id)->update(['holding_id' => null, 'holding_type' => null]);

            // Also return any currency attached to the trade
            // This is stored in the data attribute
            $currencyManager = new CurrencyManager;
            $tradeData = $trade->data;
            $isSender = ($trade->sender_id == $user->id);
            if($isSender && isset($tradeData['sender']) && isset($tradeData['sender']['currencies'])) {
                foreach($tradeData['sender']['currencies'] as $currencyId=>$quantity) {
                    $currencyManager->creditCurrency(null, $user, null, null, $currencyId, $quantity);
                }
            }

            // Unattach characters too
            Character::where('trade_id', $trade->id)->where('user_id', $user->id)->update(['trade_id' => null]);

            $userAssets = createAssetsArray();
            $assetCount = 0;
            $assetLimit = Config::get('lorekeeper.settings.trade_asset_limit');
            
            // Attach items. Technically, the user doesn't lose ownership of the item - we're just adding an additional holding field.
            // Unlike for design updates, we're keeping track of attached items here.
            if(isset($data['stack_id'])) {
                foreach($data['stack_id'] as $stackId) {
                    $stack = UserItem::with('item')->where('id', $stackId)->whereNull('holding_type')->first();
                    if(!$stack || $stack->user_id != $user->id) throw new \Exception("Invalid item selected.");
                    if(!$stack->item->allow_transfer || isset($stack->data['disallow_transfer'])) throw new \Exception("One or more of the selected items cannot be transferred.");
                    $stack->holding_type = 'Trade';
                    $stack->holding_id = $trade->id;
                    $stack->save();

                    addAsset($userAssets, $stack, 1);
                    $assetCount++;
                }
            }
            if($assetCount > $assetLimit) throw new \Exception("You may only include a maximum of {$assetLimit} things in a trade.");

            // Attach currencies. Character currencies cannot be attached to trades, so we're just checking the user's bank.
            if(isset($data['currency_id'])) {
                if ($user->id != $trade->sender_id && $user->id != $trade->recipient_id) throw new \Exception("Error attaching currencies to this trade.");
                //dd([$data['currency_id'], $data['currency_quantity']]);
                $data['currency_id'] = $data['currency_id']['user-'.$user->id];
                $data['currency_quantity'] = $data['currency_quantity']['user-'.$user->id];
                foreach($data['currency_id'] as $key=>$currencyId) {
                    $currency = Currency::where('allow_user_to_user', 1)->where('id', $currencyId)->first();
                    if(!$currency) throw new \Exception("Invalid currency selected.");
                    if(!$currencyManager->debitCurrency($user, null, null, null, $currency, $data['currency_quantity'][$key])) throw new \Exception("Invalid currency/quantity selected.");

                    addAsset($userAssets, $currency, $data['currency_quantity'][$key]);
                    $assetCount++;
                }
            }
            if($assetCount > $assetLimit) throw new \Exception("You may only include a maximum of {$assetLimit} things in a trade.");

            // Attach characters.
            if(isset($data['character_id'])) {
                foreach($data['character_id'] as $characterId) {
                    $character = Character::where('id', $characterId)->where('user_id', $user->id)->first();
                    if(!$character) throw new \Exception("Invalid character selected.");
                    if(!$character->is_sellable && !$character->is_tradeable && !$character->is_giftable) throw new \Exception("One or more of the selected characters cannot be transferred.");
                    if($character->trade_id) throw new \Exception("One or more of the selected characters is already in a trade.");
                    if($character->designUpdate()->active()->exists()) throw new \Exception("One or more of the selected characters has an active design update. Please wait for it to be processed, or delete it.");
                    if($character->transferrable_at->isFuture()) throw new \Exception("One or more of the selected characters is still on transfer cooldown and cannot be transferred.");

                    $character->trade_id = $trade->id;
                    $character->save();

                    addAsset($userAssets, $character, 1);
                    $assetCount++;
                }

            }
            if($assetCount > $assetLimit) throw new \Exception("You may only include a maximum of {$assetLimit} things in a trade.");
            
            return $this->commitReturn([($isSender ? 'sender' : 'recipient') => $userAssets]);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    public function cancelTrade($data, $user)
    {
        DB::beginTransaction();

        try {
            if(!isset($data['trade'])) $trade = Trade::where('status', 'Open')->where('id', $data['id'])->where(function($query) use ($user) {
                $query->where('sender_id', $user->id)->orWhere('recipient_id', $user->id);
            })->first();
            elseif($data['trade']->status == 'Open') $trade = $data['trade'];
            else $trade = null;
            if(!$trade) throw new \Exception("Invalid trade.");

            if($this->returnAttachments($trade)) {
                Notifications::create('TRADE_CANCELED', $trade->sender_id == $user->id ? $trade->recipient : $trade->sender, [
                    'sender_url' => $user->url,
                    'sender_name' => $user->name,
                    'trade_id' => $trade->id
                ]);
                return $this->commitReturn($trade);
            }
            else throw new \Exception("Failed to cancel trade.");
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    public function confirmOffer($data, $user)
    {
        DB::beginTransaction();

        try {
            if(!isset($data['trade'])) $trade = Trade::where('status', 'Open')->where('id', $data['id'])->where(function($query) use ($user) {
                $query->where('sender_id', $user->id)->orWhere('recipient_id', $user->id);
            })->first();
            elseif($data['trade']->status == 'Open') $trade = $data['trade'];
            else $trade = null;
            if(!$trade) throw new \Exception("Invalid trade.");

            // Mark the offer as confirmed
            if($trade->sender_id == $user->id) {
                $trade->is_sender_confirmed = !$trade->is_sender_confirmed;

                // Reset trade confirmations if this is unconfirming an offer
                if(!$trade->is_sender_confirmed) {
                    $trade->is_sender_trade_confirmed = 0;
                    $trade->is_recipient_trade_confirmed = 0;
                }
                else {
                    Notifications::create('TRADE_UPDATE', $trade->recipient, [
                        'sender_url' => $user->url,
                        'sender_name' => $user->name,
                        'trade_id' => $trade->id
                    ]);
                }
            }
            else {
                $trade->is_recipient_confirmed = !$trade->is_recipient_confirmed;

                // Reset trade confirmations if this is unconfirming an offer
                if(!$trade->is_recipient_confirmed) {
                    $trade->is_sender_trade_confirmed = 0;
                    $trade->is_recipient_trade_confirmed = 0;
                }
                else {
                    Notifications::create('TRADE_UPDATE', $trade->sender, [
                        'sender_url' => $user->url,
                        'sender_name' => $user->name,
                        'trade_id' => $trade->id
                    ]);
                }
            }

            $trade->save();

            return $this->commitReturn($trade);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    public function confirmTrade($data, $user)
    {
        DB::beginTransaction();

        try {
            if(!isset($data['trade'])) $trade = Trade::where('status', 'Open')->where('id', $data['id'])->where(function($query) use ($user) {
                $query->where('sender_id', $user->id)->orWhere('recipient_id', $user->id);
            })->first();
            elseif($data['trade']->status == 'Open') $trade = $data['trade'];
            else $trade = null;
            if(!$trade) throw new \Exception("Invalid trade.");
            if(!$trade->isConfirmable) throw new \Exception('Both parties are required to confirm their offers before the trade can be confirmed.');

            if($user->id == $trade->sender_id) $trade->is_sender_trade_confirmed = 1;
            else $trade->is_recipient_trade_confirmed = 1;

            if($trade->is_sender_trade_confirmed && $trade->is_recipient_trade_confirmed) {
                if(!(Config::get('open_transfers_queue') && (isset($trade->data['sender']['characters'])|| isset($trade->data['recipient']['characters'])))) {
                    // Distribute the trade attachments
                    $this->creditAttachments($trade);
    
                    $trade->status = 'Completed';

                    // Notify both users
                    Notifications::create('TRADE_COMPLETED', $trade->sender, [
                        'trade_id' => $trade->id
                    ]);
                    Notifications::create('TRADE_COMPLETED', $trade->recipient, [
                        'trade_id' => $trade->id
                    ]);
                }
                else {
                    // Put the trade into the queue
                    $trade->status = 'Pending';
                    
                    // Notify both users
                    Notifications::create('TRADE_CONFIRMED', $trade->sender, [
                        'trade_id' => $trade->id
                    ]);
                    Notifications::create('TRADE_CONFIRMED', $trade->recipient, [
                        'trade_id' => $trade->id
                    ]);
                }
            }
            $trade->save();

            return $this->commitReturn($trade);
        } catch(\Exception $e) { 
            dd($e->getMessage());
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    public function approveTrade($data, $user)
    {
        DB::beginTransaction();

        try {
            // 1. check that the trade exists
            // 2. check that the trade is open
            if(!isset($data['trade'])) $submission = Trade::where('status', 'Open')->where('id', $data['id'])->where('is_sender_confirmed', 1)->where('is_recipient_confirmed', 1)->first();
            elseif($data['trade']->status == 'Pending') $trade = $data['trade'];
            else $trade = null;
            if(!$trade) throw new \Exception("Invalid trade.");
            

            return $this->commitReturn($trade);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    public function rejectTrade($data, $user)
    {
        DB::beginTransaction();

        try {
            

            return $this->commitReturn($trade);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    private function clearTrade($trade)
    {
        DB::beginTransaction();

        try {
            // Returns all attached assets to their owners

            return $this->commitReturn($trade);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    private function returnAttachments($trade)
    {
        DB::beginTransaction();

        try {
            // Return all added items/currency/characters
            UserItem::where('holding_type', 'Trade')->where('holding_id', $trade->id)->update(['holding_type' => null, 'holding_id' => null]);
            Character::where('trade_id', $trade->id)->update(['trade_id' => null]);
            $tradeData = $trade->data;
            $currencyManager = new CurrencyManager;
            foreach(['sender', 'recipient'] as $type) {
                if(isset($tradeData[$type]['currencies'])) {
                    foreach($tradeData[$type]['currencies'] as $currencyId => $quantity) {
                        $currency = Currency::find($currencyId);
                        if(!$currency) throw new \Exception("Cannot return an invalid currency. (".$currencyId.")");
                        if(!$currencyManager->creditCurrency(null, $trade->{$type}, null, null, $currency, $quantity)) throw new \Exception("Could not return currency to user. (".$currencyId.")");                    
                    }
                }
            }
            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    private function creditAttachments($trade, $data = [])
    {
        DB::beginTransaction();

        try {
            $inventoryManager = new InventoryManager;

            // Get all items
            $stacks = UserItem::where('holding_type', 'Trade')->where('holding_id', $trade->id)->where('user_id', $trade->sender_id)->get();
            foreach($stacks as $stack) $inventoryManager->moveStack($trade->sender, $trade->recipient, 'Trade', ['data' => 'Received in trade (<a href="'.$trade->url.'">#'.$trade->id.'</a>).'], $stack);
            
            $stacks = UserItem::where('holding_type', 'Trade')->where('holding_id', $trade->id)->where('user_id', $trade->recipient_id)->get();
            foreach($stacks as $stack) $inventoryManager->moveStack($trade->recipient, $trade->sender, 'Trade', ['data' => 'Received in trade (<a href="'.$trade->url.'">#'.$trade->id.').'], $stack);

            UserItem::where('holding_type', 'Trade')->where('holding_id', $trade->id)->update(['holding_type' => null, 'holding_id' => null]);

            $characterManager = new CharacterManager;

            // Transfer characters
            $cooldowns = isset($data['cooldowns']) ? $data['cooldowns'] : [];
            $defaultCooldown = Settings::get('transfer_cooldown');

            $characters = Character::where('user_id', $trade->sender_id)->where('trade_id', $trade->id)->get();
            foreach($characters as $character) $characterManager->moveCharacter($character, $trade->recipient, '<a href="'.$trade->url.'">#'.$trade->id.'</a>', isset($cooldowns[$character->id]) ? $cooldowns[$character->id] : $defaultCooldown, 'Trade');
            
            $characters = Character::where('user_id', $trade->recipient_id)->where('trade_id', $trade->id)->get();
            foreach($characters as $character) $characterManager->moveCharacter($character, $trade->sender, '<a href="'.$trade->url.'">#'.$trade->id.'</a>', isset($cooldowns[$character->id]) ? $cooldowns[$character->id] : $defaultCooldown, 'Trade');

            Character::where('trade_id', $trade->id)->update(['trade_id' => null]);

            // Transfer currency
            $tradeData = $trade->data;
            $currencyManager = new CurrencyManager;
            foreach(['sender', 'recipient'] as $type) {
                if(isset($tradeData[$type]['currencies'])) {
                    foreach($tradeData[$type]['currencies'] as $currencyId => $quantity) {
                        $currency = Currency::find($currencyId);
                        if(!$currency) throw new \Exception("Cannot credit an invalid currency. (".$currencyId.")");
                        if(!$currencyManager->creditCurrency(null, $trade->{$type}, 'Trade', 'Received in trade (<a href="'.$trade->url.'">#'.$trade->id.').', $currency, $quantity)) throw new \Exception("Could not credit currency. (".$currencyId.")");                    
                    }
                }
            }
            
            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}