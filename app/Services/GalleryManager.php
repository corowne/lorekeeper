<?php namespace App\Services;

use DB;
use Image;
use Settings;
use Config;
use Carbon\Carbon;
use App\Services\Service;

use App\Models\Gallery\Gallery;
use App\Models\Gallery\GallerySubmission;
use App\Models\Gallery\GalleryCharacter;
use App\Models\Gallery\GalleryCollaborator;

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
            if(isset($data['collaborator_id'][0])) {
                $collaborators = User::whereIn('id', $data['collaborator_id'])->get();
                if(count($collaborators) != $data['collaborator_id']) throw new \Exception("One or more of the selected users does not exist.");
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

            $data = $this->populateData($data, $currencyFormData);
            $submission->update($data);

            if($data['image'] != null) $this->processImage($data, $submission);
            $submission->update();

            // Attach any collaborators to the submission
            foreach($collaborators as $key=>$collaborator) {
                GalleryCollaborator::create([
                    'user_id' => $collaborator->id,
                    'gallery_submission_id' => $submission->id,
                    'data' => $data['collaborator_data'][$key],
                    'has_approved' => $collaborator->user->id == $user->id ? 1 : 0,
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
     * Processes user input for creating/updating a gallery submission.
     *
     * @param  array                                  $data 
     * @param  array                                  $currencyFormData
     * @param  \App\Models\Gallery\GallerySubmission  $submission
     * @return array
     */
    private function populateData($data, $currencyFormData = null)
    {
        // Parse any text
        if(isset($data['text']) && $data['text']) $data['parsed_text'] = parse($data['text']);
        if(isset($data['description']) && $data['description']) $data['parsed_description'] = parse($data['description']);

        if(isset($currencyFormData) && $currencyFormData) {
            $data['data']['currencyData'] = $currencyFormData;
            $data['data']['total'] = calculateGroupCurrency($currencyFormData);
            $data['data'] = collect($data['data'])->toJson();
        }

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
            unlink($submission->imagePath . '/' . $submission->imageFileName);
        }
        $submission->hash = randomString(10);
        $submission->extension = $data['image']->getClientOriginalExtension();

        // Save image itself
        $this->handleImage($data['image'], $submission->imageDirectory, $submission->imageFileName);
        
        // Process thumbnail
        $thumbnail = Image::make($submission->imagePath . '/' .  $submission->imageFileName);

        // Resize based on larger dimension
        $imageWidth = $thumbnail->width();
        $imageHeight = $thumbnail->height();
        if($imageWidth > $imageHeight) {
            // Landscape
            $thumbnail->resize(Config::get('lorekeeper.settings.masterlist_thumbnails.width'), null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }
        else {
            // Portrait
            $thumbnail->resize(null, Config::get('lorekeeper.settings.masterlist_thumbnails.height'), function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        // Save thumbnail
        $thumbnail->save($submission->thumbnailPath . '/' . $submission->thumbnailFileName);

        return $submission;
    }

}
