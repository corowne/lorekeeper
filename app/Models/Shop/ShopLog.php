<?php

namespace App\Models\Shop;

use App\Models\Model;

class ShopLog extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shop_id', 'character_id', 'user_id', 'currency_id', 'cost', 'item_id', 'quantity'
    ];
    protected $table = 'shop_log';
    public $timestamps = true;
    
    public static $createRules = [
        'stock_id' => 'required',
        'shop_id' => 'required',
        'bank' => 'required|in:user,character'
    ];
    
    public function user() 
    {
        return $this->belongsTo('App\Models\User\User');
    }
    
    public function character() 
    {
        return $this->belongsTo('App\Models\Character\Character');
    }

    public function item() 
    {
        return $this->belongsTo('App\Models\Item\Item');
    }
    
    public function shop() 
    {
        return $this->belongsTo('App\Models\Shop\Shop');
    }
    
    public function currency() 
    {
        return $this->belongsTo('App\Models\Currency\Currency');
    }

    public function getItemDataAttribute()
    {
        return 'Purchased from '.$this->shop->name.' by '.($this->character_id ? $this->character->code . ' (owned by ' . $this->user->name . ')' : $this->user->displayName) . ' for ' . $this->cost . ' ' . $this->currency->name . '.';
    }
}
