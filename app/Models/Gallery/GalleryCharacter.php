<?php

namespace App\Models\Gallery;

use App\Models\Character\Character;
use App\Models\Model;

class GalleryCharacter extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'gallery_submission_id', 'character_id',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'gallery_submission_characters';

    /**
     * Get the submission this is attached to.
     */
    public function submission() {
        return $this->belongsTo(GallerySubmission::class, 'gallery_submission_id');
    }

    /**
     * Get the character being attached to the submission.
     */
    public function character() {
        return $this->belongsTo(Character::class, 'character_id');
    }
}
