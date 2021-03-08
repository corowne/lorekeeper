<?php

namespace App\Models;

use Auth;
use Config;
use App\Models\Model;
use App\Traits\Commentable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Forum extends Model
{
    use SoftDeletes;
    use Commentable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'parsed_description', 'is_locked', 'staff_only', 'role_limit', 'parent_id', 'has_image', 'extension', 'sort', 'is_active'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'forums';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the parent of this forum.
     */
    public function parent()
    {
        return $this->belongsTo('App\Models\Forum');
    }


    /**
     * Get the children of this forum.
     */
    public function children()
    {
        return $this->hasMany('App\Models\Forum', 'parent_id');
    }
    /**
     * Get the children of this forum.
     */
    public function role()
    {
        return $this->belongsTo('App\Models\Rank\Rank', 'role_limit');
    }


    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope only forums with no parent_id
     */
    public function scopeCategory($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope forums that are staff only.
     */
    public function scopeStaff($query, $only = false)
    {
        if($only){
            if(Auth::check() && Auth::user()->isStaff) return $query->where('staff_only',1);
            return $query->where('staff_only',0);
        }
        else {
            if(Auth::check() && Auth::user()->isStaff) return $query;
            return $query->where('staff_only',0);
        }
    }

    /**
     * Scope forums are locked for new posts/comments.
     */
    public function scopeLocked($query, $state = 1)
    {
        return $query->where('is_locked',$state);
    }

    /**
     * Scope forums are locked for new posts/comments.
     */
    public function scopeVisible($query, $state = 1)
    {
        if(!Auth::check() || !(Auth::check() && Auth::user()->isStaff))  return $query->where('is_active',$state)->where('staff_only',0);
        else return $query->where('is_active',$state);
    }


    /**********************************************************************************************

        ATTRIBUTES

    **********************************************************************************************/

    /**
     * Gets the forum url.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('forum/'.$this->id);
    }

    /**
     * Displays the forum's name, linked to its page.
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        $icon = [];
        if($this->is_locked) $icon[] = '<i class="fas fa-lock mr-1" data-toggle="tooltip" title="This forum is locked."></i>';
        if($this->staff_only) $icon[] = '<i class="fas fa-crown mr-1" data-toggle="tooltip" title="Staff-only Forum."></i>';
        if($this->role) $icon[] = '<i class="fas fa-star mr-1" data-toggle="tooltip" title="'. $this->role->name .'-only Forum."></i>';
        $icon = (isset($icon) ? implode('',$icon) : '');

        if($this->is_locked)
        {
            if(Auth::check() && Auth::user()->isStaff) return '<a href="'.$this->url.'" >'. $icon . $this->name .'</a>';
            else return '<span>'. $icon . $this->name . '</span>';
        }
        else return '<a href="'.$this->url.'" class="display-forum">'. $icon . $this->name .'</a>';
    }

    /**
     * Determines if Forum has any restrictions
     *
     * @return string
     */
    public function getHasRestrictionsAttribute()
    {
        if($this->is_locked || $this->staff_only || $this->role) return true;
        else return false;
    }

    public function getAccessibleSubforumsAttribute()
    {
        $children = collect();
        if($this->children) {
            foreach($this->children as $child)
            {
                if(!$child->hasRestrictions || Auth::check() && Auth::user()->canVisitForum($child->id)) {
                    $children->push($child);
                }
            }
        }
        return $children;
    }

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute()
    {
        return 'images/data/forums';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getImageFileNameAttribute()
    {
        return $this->id . '-image.'.$this->extension;
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
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getCommentsAttribute()
    {
        return Comment::where('commentable_type','App\Models\Forum')->where('commentable_id',$this->id)->get();
    }

    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/

    /**
     * Checks if a board is locked
     */
    public function canUsersPost($board = null)
    {
        if($board == null) $board = $this;
        if($board->is_locked) return false;
        elseif(isset($board->parent_id)) {
            if(!$board->canUsersPost($board->parent)) return false;
        }
        return true;
    }



}
