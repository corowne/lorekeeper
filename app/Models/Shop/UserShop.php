<?php

namespace App\Models\Shop;

use Config;
use App\Models\Model;

class UserShop extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'user_id','sort', 'has_image', 'description', 'parsed_description', 'is_active','updated_at'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_shops';
    
    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required|unique:user_shops|between:3,25',
        'description' => 'nullable',
        'image' => 'mimes:png',
    ];
    
    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => 'required|between:3,25',
        'description' => 'nullable',
        'image' => 'mimes:png',
    ];

    /**********************************************************************************************
    
        RELATIONS

    **********************************************************************************************/

    /**
     * Get the shop stock.
     */
    public function stock() 
    {
        return $this->hasMany('App\Models\Shop\UserShopStock')->orderBy('id', 'DESC');
    }

    /**
     * Get the shop stock (visible & non 0 quantity for public display)
     */
    public function visibleStock() 
    {
        return $this->hasMany('App\Models\Shop\UserShopStock')->where('quantity', '>', 0)->where('is_visible', 1);
    }

    /**
     * Get the user who owns the character.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User\User', 'user_id');
    }
    /**
     * Get the shop stock as items for display purposes.
     */
    public function displayStock()
    {
        return $this->belongsToMany('App\Models\Item\Item', 'user_shop_stock')->where('stock_type', 'Item')->withPivot('item_id', 'currency_id', 'cost', 'quantity', 'id', 'is_visible')->wherePivot('quantity', '>', 0)->wherePivot('is_visible', 1);
    }

    /**
     * Get the user logs attached to this code.
     */
    public function buyers()
    {
        return $this->hasMany('App\Models\Shop\UserShopLog', 'user_shop_id');
    }

    /**
     * Scope a query to show only visible features.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query, $user = null) {
        
        if ($user && $user->hasPower('edit_inventories')) {
            return $query;
        }

        return $query->where('is_active', 1)->whereRelation('user', 'is_banned', 0);
    }

    /**
     * Gets the user's log type for log creation.
     *
     * @return string
     */
    public function getLogTypeAttribute()
    {
        return 'Shop';
    }

    /**********************************************************************************************
    
        ACCESSORS

    **********************************************************************************************/
    
    /**
     * Displays the shop's name, linked to its purchase page.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return (!$this->is_active ? '<i class="fas fa-eye-slash mr-1"></i>' : '') .'<a href="'.$this->url.'" class="display-shop">'.$this->name.'</a>';
    }

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute()
    {
        return 'images/data/usershops';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getShopImageFileNameAttribute()
    {
        return $this->id . '-image.png';
    }

    /**
     * Gets the path to the file directory containing the model's image.
     *
     * @return string
     */
    public function getShopImagePathAttribute()
    {
        return public_path($this->imageDirectory);
    }
    
    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getShopImageUrlAttribute()
    {
        if (!$this->has_image) return null;
        return asset($this->imageDirectory . '/' . $this->shopImageFileName);
    }

    /**
     * Gets the URL of the model's encyclopedia page.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('/user-shops/shop/'.$this->id);
    }

    /**
     * Get the shop's shop sale logs.
     *
     * @param  int  $limit
     * @return \Illuminate\Support\Collection|\Illuminate\Pagination\LengthAwarePaginator
     */
    public function getShopLogs($limit = 10)
    {
        $user = $this;
        $query = UserShopLog::where('user_shop_id', $this->id)->with('shop')->with('item')->with('currency')->orderBy('id', 'DESC');
        if($limit) return $query->take($limit)->get();
        else return $query->paginate(30);
    }

        /**
     * Gets the URL to edit shop
     *
     * @return string
     */
    public function getEditUrlAttribute() {
        return url('/user-shops/edit/'.$this->id);
    }
}
