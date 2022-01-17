<?php

namespace App\Models\Sales;

use Carbon\Carbon;
use Config;
use App\Models\Model;
use App\Traits\Commentable;
use Illuminate\Support\Str;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;

class Sales extends Model implements Feedable
{
    use Commentable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'text', 'parsed_text', 'title', 'is_visible', 'post_at',
        'is_open', 'comments_open_at'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sales';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**
     * Dates on the model to convert to Carbon instances.
     *
     * @var array
     */
    public $dates = ['post_at', 'comments_open_at'];

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'title' => 'required|between:3,100',
        'text' => 'required',
    ];

    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'title' => 'required|between:3,100',
        'text' => 'required',
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the user who created the Sales post.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User\User');
    }

    /**
     * Get the characters associated with the sales post.
     */
    public function characters()
    {
        return $this->hasMany('App\Models\Sales\SalesCharacter', 'sales_id');
    }

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to only include visible posts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', 1);
    }

    /**
     * Scope a query to only include posts that are scheduled to be posted and are ready to post.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeShouldBeVisible($query)
    {
        return $query->whereNotNull('post_at')->where('post_at', '<', Carbon::now())->where('is_visible', 0);
    }

    
    /**
     * Scope a query to sort sales in alphabetical order.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  bool                                   $reverse
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortAlphabetical($query, $reverse = false)
    {
        return $query->orderBy('title', $reverse ? 'DESC' : 'ASC');
    }

    /**
     * Scope a query to sort sales by newest first.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortNewest($query)
    {
        return $query->orderBy('id', 'DESC');
    }

    /**
     * Scope a query to sort sales oldest first.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortOldest($query)
    {
        return $query->orderBy('id');
    }

    /**
     * Scope a query to sort sales by bump date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  bool                                   $reverse
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortBump($query, $reverse = false)
    {
        return $query->orderBy('updated_at', $reverse ? 'DESC' : 'ASC');
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Get the Sales slug.
     *
     * @return bool
     */
    public function getSlugAttribute()
    {
        return $this->id . '.' . Str::slug($this->title);
    }

    /**
     * Displays the Sales post title, linked to the Sales post itself.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return '<a href="'.$this->url.'"> ['.($this->is_open ? (isset($this->comments_open_at) && $this->comments_open_at > Carbon::now() ? 'Preview' : 'Open') : 'Closed').'] '.$this->title.'</a>';
    }

    /**
     * Gets the Sales post URL.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('sales/'.$this->slug);
    }

    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/

    /**
     * Returns all feed items.
     *
     */
    public static function getFeedItems()
    {
        return Sales::visible()->get();
    }

    /**
     * Generates feed item information.
     *
     * @return /Spatie/Feed/FeedItem;
     */
    public function toFeedItem(): FeedItem
    {
        $summary = ($this->characters->count() ? $this->characters->count().' character'.($this->characters->count() > 1 ? 's are' : ' is').' associated with this sale. Click through to read more.<hr/>' : '').$this->parsed_text;

        return FeedItem::create([
            'id' => '/sales/'.$this->id,
            'title' => $this->title,
            'summary' => $summary,
            'updated' => $this->updated_at,
            'link' => $this->url,
            'author' => $this->user->name
        ]);
    }
}
