<?php namespace App\Services;

use DB;
use Image;
use Settings;
use Config;
use Notifications;
use Carbon\Carbon;
use App\Services\Service;

use App\Models\Gallery\Gallery;
use App\Models\Gallery\GallerySubmission;
use App\Models\Gallery\GalleryCharacter;
use App\Models\Gallery\GalleryCollaborator;
use App\Models\Gallery\GalleryFavorite;

use App\Models\User\User;
use App\Models\Character\Character;
use App\Models\Prompt\Prompt;

class GalleryManager extends Service 
{
    /*
    |--------------------------------------------------------------------------
    | Gallery Manager
    |--------------------------------------------------------------------------
    |
    | Handles creation and modification of gallery submissions.
    |
    */

    /**
     * Creates a new gallery submission.
     *
     * @param  array                  $data 
     * @param  array                  $currencyFormData
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Gallery\GallerySubmission
     */
    public function createSubmission($data, $currencyFormData, $user)
    {
        DB::beginTransaction();

        try {
            //  Check that submissions are open
            if(!Settings::get('gallery_submissions_open')) throw new \Exception("Gallery submissions are currently closed.");
            // Check that the gallery exists and can be submitted to
            $gallery = Gallery::find($data['gallery_id']);
            if(!$gallery) throw new \Exception ("Invalid gallery selected.");
            if(!$gallery->submissions_open && !$user->hasPower('manage_submissions')) throw new \Exception("You cannot submit to this gallery.");

            // Check that associated collaborators exist
            if(isset($data['collaborator_id'])) {
                $collaborators = User::whereIn('id', $data['collaborator_id'])->get();
                if(count($collaborators) != count($data['collaborator_id'])) throw new \Exception("One or more of the selected users does not exist.");
            }
            else $collaborators = [];

            // Check that associated characters exist
            if(isset($data['slug'])) {
                $characters = Character::myo(0)->visible()->whereIn('slug', $data['slug'])->get();
                if(count($characters) != count($data['slug'])) throw new \Exception("One or more of the selected characters does not exist.");
            }
            else $characters = [];

            // Check that the selected prompt exists and can be submitted to
            if(isset($data['prompt_id'])) {
                $prompt = Prompt::active()->find($id);
                if(!$prompt) throw new \Exception("Invalid prompt selected.");
            }

            $submission = GallerySubmission::create([
                'user_id' => $user->id,
                'gallery_id' => $gallery->id,
                'status' => 'Pending',
                'title' => $data['title'],
                'is_visible' => 1
            ]);

            $data = $this->populateData($data);

            if(isset($currencyFormData) && $currencyFormData) {
                $data['data']['currencyData'] = $currencyFormData;
                $data['data']['total'] = calculateGroupCurrency($currencyFormData);
                $data['data'] = collect($data['data'])->toJson();
            }

            $submission->update($data);

            if(isset($data['image']) && $data['image']) $this->processImage($data, $submission);
            $submission->update();

            // Attach any collaborators to the submission
            foreach($collaborators as $key=>$collaborator) {
                GalleryCollaborator::create([
                    'user_id' => $collaborator->id,
                    'gallery_submission_id' => $submission->id,
                    'data' => $data['collaborator_data'][$key],
                    'has_approved' => $collaborator->id == $user->id ? 1 : 0,
                ]);

                // Notify collaborators (but not the submitting user)
                if($collaborator->user->id != $user->id) {
                    Notifications::create('GALLERY_COLLABORATOR', $collaborator->user, [
                        'sender_url' => $user->url,
                        'sender' => $user->name,
                        'submission_id' => $submission->id,
                    ]);
                }
            }

            // Attach any characters to the submission
            foreach($characters as $character) {
                GalleryCharacter::create([
                    'character_id' => $character->id,
                    'gallery_submission_id' => $submission->id,
                ]);
            }

            return $this->commitReturn($submission);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates a gallery submission.
     *
     * @param  \App\Models\Gallery\GallerySubmission  $submission
     * @param  array                                  $data 
     * @param  \App\Models\User\User                  $user
     * @return bool|\App\Models\Gallery\GallerySubmission
     */
    public function updateSubmission($submission, $data, $user)
    {
        DB::beginTransaction();

        try {
            // Check that the user can edit the submission
            if(!$submission->user_id == $user->id && !$user->hasPower('manage_submissions')) throw new \Exception("You can't edit this submission.");
            
            // Check that there is text and/or an image, including if there is an existing image (via the existence of a hash)
            if((!isset($data['image']) && !isset($submission->hash)) && !$data['text']) throw new \Exception("Please submit either text or an image.");
            
            // If still pending, perform validation on and process collaborators
            if($submission->status == 'Pending') { 
                // Check that associated collaborators exist
                if(isset($data['collaborator_id'])) {
                    $collaborators = User::whereIn('id', $data['collaborator_id'])->get();
                    if(count($collaborators) != count($data['collaborator_id'])) throw new \Exception("One or more of the selected users does not exist.");
                }
                else $collaborators = [];

                // Fetch collaborator approval data
                $collaboratorApproval = $submission->collaborators->pluck('has_approved', 'user_id');
                // Remove all collaborators from the submission so they can be reattached with new data
                $submission->collaborators()->delete();

                // Attach any collaborators to the submission
                foreach($collaborators as $key=>$collaborator) {
                    GalleryCollaborator::create([
                        'user_id' => $collaborator->id,
                        'gallery_submission_id' => $submission->id,
                        'data' => $data['collaborator_data'][$key],
                        'has_approved' => isset($collaboratorApproval[$collaborator->id]) ? $collaboratorApproval[$collaborator->id] : ($collaborator->id == $user->id ? 1 : 0),
                    ]);
                }
            }

            // Check that associated characters exist
            if(isset($data['slug'])) {
                $characters = Character::myo(0)->visible()->whereIn('slug', $data['slug'])->get();
                if(count($characters) != count($data['slug'])) throw new \Exception("One or more of the selected characters does not exist.");
            }
            else $characters = [];

            // Remove all characters from the submission so they can be reattached with new data
            $submission->characters()->delete();

            // Attach any characters to the submission
            foreach($characters as $character) {
                GalleryCharacter::create([
                    'character_id' => $character->id,
                    'gallery_submission_id' => $submission->id,
                ]);
            }

            $data = $this->populateData($data);
            if(isset($data['image']) && $data['image']) $this->processImage($data, $submission);

            $submission->update($data);

            return $this->commitReturn($submission);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Processes user input for creating/updating a gallery submission.
     *
     * @param  array                                  $data
     * @return array
     */
    private function populateData($data)
    {
        // Parse any text
        if(isset($data['text']) && $data['text']) $data['parsed_text'] = parse($data['text']);
        if(isset($data['description']) && $data['description']) $data['parsed_description'] = parse($data['description']);

        return $data;
    }

    /**
     * Processes gallery submission images.
     *
     * @param  array                                  $data
     * @param  \App\Models\Gallery\GallerySubmission  $submission
     * @return array
     */
    private function processImage($data, $submission)
    {
        if(isset($submission->hash)) {
            unlink($submission->imagePath . '/' . $submission->imageFileName);
            unlink($submission->imagePath . '/' . $submission->thumbnailFileName);
        }
        $submission->hash = randomString(10);
        $submission->extension = $data['image']->getClientOriginalExtension();

        // Save image itself
        $this->handleImage($data['image'], $submission->imageDirectory, $submission->imageFileName);
        
        // Process thumbnail
        $thumbnail = Image::make($submission->imagePath . '/' .  $submission->imageFileName);
        // Resize
        $thumbnail->resize(null, Config::get('lorekeeper.settings.masterlist_thumbnails.height'), function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // Save thumbnail
        $thumbnail->save($submission->thumbnailPath . '/' . $submission->thumbnailFileName);

        return $submission;
    }

    /**
     * Processes collaborator edits/approvals on a submission.
     *
     * @param  \App\Models\Gallery\GallerySubmission  $submission
     * @param  \App\Models\User\User                  $user
     * @return bool|\App\Models\Gallery\GalleryFavorite
     */
    public function editCollaborator($submission, $data, $user)
    {
        DB::beginTransaction();

        try {
            // Check that the submission is pending and the user is a collaborator on it
            if(!$submission->status == 'Pending') throw new \Exception("This submission isn't pending.");
            if($submission->collaborators->where('user_id', $user->id)->first() == null) throw new \Exception("You aren't a collaborator on this submission."); 

            // Check if the user has requested to be removed from the submission
            if(isset($data['remove_user']) && $data['remove_user']) {
                $submission->collaborators()->where('user_id', $user->id)->delete();
            }
            // Otherwise update the record of their contribution and mark them as having approved
            else {
                $collaborator = $submission->collaborators->where('user_id', $user->id)->first();
                $collaboratorData = ['data' => $data['collaborator_data'][0], 'has_approved' => 1];
                $collaborator->update($collaboratorData);

                // Check if all collaborators have approved, and if so send a notification to the
                // submitting user (unless they are the last to approve-- which shouldn't happen, but)
                if(!$submission->collaborators->where('has_approved', 0)->count()) {
                    if($submission->user->id != $user->id) {
                        Notifications::create('GALLERY_COLLABORATORS_APPROVED', $submission->user, [
                            'submission_title' => $submission->title,
                            'submission_id' => $submission->id,
                        ]);
                    }
                }
            }

            return $this->commitReturn($submission);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Toggles favorite status on a submission for a user.
     *
     * @param  \App\Models\Gallery\GallerySubmission  $submission
     * @param  \App\Models\User\User                  $user
     * @return bool|\App\Models\Gallery\GalleryFavorite
     */
    public function favoriteSubmission($submission, $user)
    {
        DB::beginTransaction();

        try {
            // Check that the submission can be favorited
            if(!$submission->isVisible) throw new \Exception("This submission isn't visible to be favorited.");
            if($submission->user->id == $user->id || $submission->collaborators->where('user_id', $user->id)->first() != null) throw new \Exception("You can't favorite your own submission!"); 

            // Check if the user has an existing favorite, and if so, delete it
            // or else create one.
            if($submission->favorites->where('user_id', $user->id)->first() != null) {
                $submission->favorites()->where('user_id', $user->id)->delete();
            }
            else {
                GalleryFavorite::create([
                    'user_id' => $user->id,
                    'gallery_submission_id' => $submission->id,
                ]);

                if($submission->user->id != $user->id) {
                    Notifications::create('GALLERY_SUBMISSION_FAVORITE', $submission->user, [
                        'sender_url' => $user->url,
                        'sender' => $user->name,
                        'submission_title' => $submission->title,
                        'submission_id' => $submission->id,
                    ]);
                }
            }

            return $this->commitReturn($submission);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

}
