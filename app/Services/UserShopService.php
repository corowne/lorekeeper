<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;
use Settings;
use Carbon\Carbon;

use App\Models\Shop\UserShop;
use App\Models\Shop\UserShopStock;
use App\Models\Currency\Currency;

class UserShopService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | User Shop Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of shops and shop stock.
    |
    */

    /**********************************************************************************************
     
        SHOPS

    **********************************************************************************************/
    
    /**
     * Creates a new shop.
     *
     * @param  array                  $data 
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Shop\UserShop
     */
    public function createShop($data, $user)
    {
        DB::beginTransaction();

        try {

            //check for shop limit, if there is one
            if(Settings::get('user_shop_limit') != 0) {
                if(UserShop::where('user_id', $user->id)->count() >= Settings::get('user_shop_limit')) throw new \Exception("You have already created the maximum number of shops.");
            }

            $data['user_id'] = $user->id;
            $data = $this->populateShopData($data);

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }
            else $data['has_image'] = 0;

            $shop = UserShop::create($data);

            if ($image) $this->handleImage($image, $shop->shopImagePath, $shop->shopImageFileName);

            return $this->commitReturn($shop);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    /**
     * Updates a shop.
     *
     * @param  \App\Models\Shop\UserShop  $shop
     * @param  array                  $data 
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Shop\UserShop
     */
    public function updateShop($shop, $data, $user)
    {
        DB::beginTransaction();

        try {
            // More specific validation
            if(UserShop::where('name', $data['name'])->where('id', '!=', $shop->id)->exists()) throw new \Exception("The name has already been taken.");

            $data = $this->populateShopData($data, $shop);

            $image = null;            
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }

            $shop->update($data);

            if ($shop) $this->handleImage($image, $shop->shopImagePath, $shop->shopImageFileName);

            return $this->commitReturn($shop);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Processes user input for creating/updating a shop.
     *
     * @param  array                  $data 
     * @param  \App\Models\Shop\UserShop  $shop
     * @return array
     */
    private function populateShopData($data, $shop = null)
    {
        if(isset($data['description']) && $data['description']) $data['parsed_description'] = parse($data['description']);
        $data['is_active'] = isset($data['is_active']);
        
        if(isset($data['remove_image']))
        {
            if($shop && $shop->has_image && $data['remove_image']) 
            { 
                $data['has_image'] = 0; 
                $this->deleteImage($shop->shopImagePath, $shop->shopImageFileName); 
            }
            unset($data['remove_image']);
        }

        return $data;
    }
    
    /**
     * Deletes a shop.
     *
     * @param  \App\Models\Shop\UserShop  $shop
     * @return bool
     */
    public function deleteShop($shop)
    {
        DB::beginTransaction();

        try {
             
            if($shop->stock->where('quantity', '>', 0)->count()) throw new \Exception("This shop currently has items stocked. Please remove them and try again.");
            //delete the 0 stock items or the shop cannot be deleted
            $shop->stock()->delete();

            if($shop->has_image) $this->deleteImage($shop->shopImagePath, $shop->shopImageFileName); 
            $shop->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Sorts shop order.
     *
     * @param  array  $data
     * @return bool
     */
    public function sortShop($data)
    {
        DB::beginTransaction();

        try {
            // explode the sort array and reverse it since the order is inverted
            $sort = array_reverse(explode(',', $data));

            foreach($sort as $key => $s) {
                UserShop::where('id', $s)->update(['sort' => $key]);
            }

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * quick edit stock
     *
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @param  bool                   $isClaim
     * @return mixed
     */
    public function quickstockStock($data, $shop, $user)
    {
        DB::beginTransaction();
        try { 

            if(isset($data['stock_id'])) {
                foreach($data['stock_id'] as $key => $itemId) {

                    if($data['cost'][$key] == null) throw new \Exception("One or more of the items is missing a cost.");
                    if($data['cost'][$key] < 0) throw new \Exception("One or more of the items has a negative cost.");

                    //check for the currency id here
                    if($data['currency_id'][$key] == null) throw new \Exception("One or more of the items is missing a currency.");
                    if((isset($data['currency_id'][$key]) && $data['currency_id'][$key]) && !Currency::where('id', $data['currency_id'][$key])->exists()) throw new \Exception("The selected currency is invalid.");

                    $stock = UserShopStock::find($itemId);
                    //update the data of the stocks
                    $stock->update([
                        'is_visible' => isset($data['is_visible'][$itemId]),
                        'cost' => $data['cost'][$key], 
                        'currency_id' => $data['currency_id'][$key]
                    ]);
                    //transfer them if qty selected
                    if(isset($data['quantity'][$key]) && $data['quantity'][$key] > 0) {
                        if(!(new InventoryManager)->sendShop($shop, $shop->user, $stock, $data['quantity'][$key])) throw new \Exception("Could not transfer item to user.");
                    }
                }
                $shop->update([
                    'updated_at' => Carbon::now()
                ]);
            }

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

}