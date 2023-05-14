<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;
use Auth;

use App\Models\Shop\UserShop;
use App\Models\Shop\UserShopLog;
use App\Models\Shop\UserShopStock; 
use Notifications;

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
            if($shop->user->id == Auth::user()->id) throw new \Exception("You can't buy from a shop that you own!");
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

            // Add a purchase log
            $shopLog = UserShopLog::create([
                'user_shop_id' => $shop->id,
                'user_id' => $user->id,
                'currency_id' => $shopStock->currency->id,
                'cost' =>  $shopStock->cost,
                'item_id' => $shopStock->item_id,
                'quantity' => $quantity
            ]);

            //log message because the logs hate me so i have to define it here
            $itemData = 'Purchased from '.$shop->displayName.' by '. $user->displayName . ' for  ' . $total_cost .' '. $shopStock->currency->name . '.';
            // Give the user the item, noting down 1. whose currency was used 2. who purchased it 3. which shop it was purchased from
            if($shopStock->stock_type == 'Item') {
                if(!(new InventoryManager)->creditItem(null, $user, 'Shop Purchase', [
                    'data' => $itemData,
                    'notes' => 'Purchased ' . format_date($shopLog->created_at),
                ], $shopStock->item, $quantity)) throw new \Exception("Failed to purchase item.");
            }
            
            //credit the currency to the shop owner
            if(!(new CurrencyManager)->creditCurrency(null, $shop->user, 'User Shop Credit', 'Sold a '.$shopStock->item->displayName.' in '.$shop->displayName, $shopStock->currency, $total_cost)) throw new \Exception("Failed to credit currency.");   
            //notify the shop owner
            Notifications::create('USER_SHOP_ITEM_SOLD', $shop->user, [
                'shop_id' => $shop->id,
                'shop_name' => $shop->name,
                'item_name' => $shopStock->item->displayName,
                'currency_name' => $shopStock->currency->name,
                'currency_quantity' => $total_cost,
            ]);

                        //after all is done, if the qty has reached 0, delete it or things will get a bit weird
                        if($shopStock->quantity == 0) {
                            $shopStock->delete();
                        }

            return $this->commitReturn($shop);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
}