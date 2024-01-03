<?php

namespace App\Models\Gallery;

use App\Models\Model;
use App\Models\User\User;

class GalleryFavorite extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'gallery_submission_id',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'gallery_favorites';

    /**
     * Get the character being attached to the submission.
     */
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the submission this is attached to.
     */
    public function submission() {
        return $this->belongsTo(GallerySubmission::class, 'gallery_submission_id');
    }
}
