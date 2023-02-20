<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use App\Models\Shop\UserShop;
use App\Models\Shop\UserShopStock; 

class UserShopManager extends Service
{
    /*
    |--------------------------------------------------------------------------
    | User Shop Manager
    |--------------------------------------------------------------------------
    |
    | Handles purchasing of items from shops.
    |
    */

    /**
     * Buys an item from a shop.
     *
     * @param  array                 $data
     * @param  \App\Models\User\User $user
     * @return bool|App\Models\Shop\UserShop
     */
    public function buyStock($data, $user)
    {
        DB::beginTransaction();

        try {
            $quantity = $data['quantity'];
            if(!$quantity || $quantity == 0) throw new \Exception("Invalid quantity selected.");

            // Check that the shop exists and is open
            $shop = UserShop::where('id', $data['user_shop_id'])->where('is_active', 1)->first();
            if(!$shop) throw new \Exception("Invalid shop selected.");

            // Check that the stock exists and belongs to the shop
            $shopStock = UserShopStock::where('id', $data['stock_id'])->where('user_shop_id', $data['user_shop_id'])->with('currency')->with('item')->first();
            if(!$shopStock) throw new \Exception("Invalid item selected.");

            // Check if the item has a quantity, and if it does, check there is enough stock remaining
            if($shopStock->quantity < $quantity) throw new \Exception("There is insufficient stock to fulfill your request.");


            $total_cost = $shopStock->cost * $quantity;

                if($shopStock->cost > 0 && !(new CurrencyManager)->debitCurrency($user, null, 'User Shop Purchase', 'Purchased '.$shopStock->item->name.' from '.$shop->name, $shopStock->currency, $total_cost)) throw new \Exception("Not enough currency to make this purchase.");

                $shopStock->quantity -= $quantity;
                $shopStock->save();

            $itemdata = 'Purchased from '.$this->shop->name.' by '.$this->user->displayName. ' for ' . $this->cost . ' ' . $this->currency->name . '.';
            
            // Give the user the item, noting down 1. whose currency was used (user or character) 2. who purchased it 3. which shop it was purchased from
            if(!(new InventoryManager)->creditItem(null, $user, 'User Shop Purchase', [
                'data' => $itemdata, 
                'notes' => 'Purchased ' . format_date(Carbon::now())
            ], $shopStock->item, $quantity)) throw new \Exception("Failed to purchase item.");

            return $this->commitReturn($shop);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * sends item to shop
     *
     * @param  \App\Models\User\User $owner
     * @param  \App\Models\User\UserItem $stacks
     * @param  int       $quantities
     * @return bool
     */
    public function sendShop($item, $id, $service)
    {
        DB::beginTransaction();

        try {
                $user = Auth::user();
                if($id == NULL) throw new \Exception("No shop selected.");
                $shop = UserShop::find($id);
                if(!$user->hasAlias) throw new \Exception("Your deviantART account must be verified before you can perform this action.");
                if(!$item) throw new \Exception("An invalid item was selected.");
                if($pet->user_id != $user->id && !$user->hasPower('edit_inventories')) throw new \Exception("You do not own this item.");
                if(!$shop) throw new \Exception("An invalid shop was selected.");
                if($shop->user_id !== $user->id && !$user->hasPower('edit_inventories'))throw new \Exception("You do not own this shop.");

                $item['user_shop_id'] = $shop->id;
                $item->save();

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
}