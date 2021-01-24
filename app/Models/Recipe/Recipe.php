<?php

namespace App\Models\Recipe;

use Config;
use DB;
use App\Models\Model;

class Recipe extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'has_image', 'needs_unlocking', 'description', 'parsed_description', 'reference_url', 'artist_alias' ,'artist_url', 'is_limited'
    ];

    protected $appends = ['image_url'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'recipes';

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name' => 'required|unique:recipes',
        'description' => 'nullable',
        'image' => 'mimes:png',
    ];

    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name' => 'required',
        'description' => 'nullable',
        'image' => 'mimes:png',
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the recipe's ingredients.
     */
    public function ingredients()
    {
        return $this->hasMany('App\Models\Recipe\RecipeIngredient');
    }

    /**
     * Get the users who have this recipe.
     */
    public function users()
    {
        return $this->belongsToMany('App\Models\User\User', 'user_recipes')->withPivot('id');
    }

    public function limits()
    {
        return $this->hasMany('App\Models\Recipe\RecipeLimit');
    }

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to sort items in alphabetical order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  bool                                   $reverse
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortAlphabetical($query, $reverse = false)
    {
        return $query->orderBy('name', $reverse ? 'DESC' : 'ASC');
    }

    /**
     * Scope a query to sort items by newest first.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortNewest($query)
    {
        return $query->orderBy('id', 'DESC');
    }

    /**
     * Scope a query to sort features oldest first.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortOldest($query)
    {
        return $query->orderBy('id');
    }


    /**
     * Scope a query to only show recipes that need to be unlocked.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortNeedsUnlocking($query)
    {
        return $query->where('needs_unlocking', 1);
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Gets the decoded output json
     *
     * @return array
     */
    public function getRewardsAttribute()
    {
        $rewards = [];
        if($this->output) {
            $assets = $this->getRewardItemsAttribute();

            foreach($assets as $type => $a)
            {
                $class = getAssetModelString($type, false);
                foreach($a as $id => $asset)
                {
                    $rewards[] = (object)[
                        'rewardable_type' => $class,
                        'rewardable_id' => $id,
                        'quantity' => $asset['quantity']
                    ];
                }
            }
        }
        return $rewards;
    }

    /**
     * Interprets the json output and retrieves the corresponding items
     *
     * @return array
     */
    public function getRewardItemsAttribute()
    {
        return parseAssetData(json_decode($this->output, true));
    }

    /**
     * Gets the URL of the individual recipe's page, by ID.
     *
     * @return string
     */
    public function getIdUrlAttribute()
    {
        return url('world/recipes/'.$this->id);
    }

    /**
     * Displays the model's name, linked to its encyclopedia page.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->idUrl.'" class="display-item">'.$this->name.'</a>';
    }

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute()
    {
        return 'images/data/recipes';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getImageFileNameAttribute()
    {
        return $this->id . '-image.png';
    }

    /**
     * Gets the path to the file directory containing the model's image.
     *
     * @return string
     */
    public function getImagePathAttribute()
    {
        return public_path($this->imageDirectory);
    }

    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        if (!$this->has_image) return null;
        return asset($this->imageDirectory . '/' . $this->imageFileName);
    }

    /**
     * Gets the URL of the model's encyclopedia page.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('world/recipes?name='.$this->name);
    }

    /**
     * Gets the currency's asset type for asset management.
     *
     * @return string
     */
    public function getAssetTypeAttribute()
    {
        return 'recipes';
    }

    /**
    * Gets the currency's asset type for asset management.
    *
    * @return bool
    */
   public function getLockedAttribute()
   {
       return $this->needs_unlocking && !User;
   }

   /**
    * Returns whether or not a recipe's ingredients are all currency
    *
    * @return bool
    */
   public function getOnlyCurrencyAttribute()
   {
        if(count($this->ingredients))
        {
            $type = [];
            foreach($this->ingredients as $ingredient)
            {
                $type[] = $ingredient->ingredient_type;
            }
            $types = array_flip($type);
            if(count($types) == 1 && key($types) == 'Currency') return true;
            else return false;
        }
        else return false;
   }
}
