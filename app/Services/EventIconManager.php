<?php

namespace App\Services;

use App\Models\EventIcon\EventIcon;
use Illuminate\Support\Facades\DB;
use Log;

class EventIconManager extends Service {
    /*
    |--------------------------------------------------------------------------
    | EventIcon Manager
    |--------------------------------------------------------------------------
    |
    | Handles uploading and manipulation of eventIcon files.
    |
    */

    /**
     * Uploads a file.
     *
     * @param array  $file
     * @param string $dir
     * @param string $name
     * @param bool   $isFileManager
     *
     * @return bool
     */
    public function createEventIcon($data, $user) {

        DB::beginTransaction();

        try {

            $image = null;
            if (isset($data['image']) && $data['image']) {
                $image = $data['image'];
                unset($data['image']);
            }

            $data['image'] = $image->getClientOriginalName();
            if(!isset($data['is_visible'])) $data['is_visible'] = 0;
            $eventIcon = EventIcon::create($data);

            Log::info($eventIcon->imagePath);
            if ($image) {
                $this->handleImage($image, $eventIcon->imagePath, $image->getClientOriginalName(), null);
            }

            return $this->commitReturn($eventIcon);

        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    public function updateEventIcon($eventIcon)
    {
        DB::beginTransaction();

        try {
            if(!isset($data['is_visible'])) $data['is_visible'] = 0;

            $eventIcon->update($data);

            return $this->commitReturn($news);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a file.
     *
     * @param string $path
     *
     * @return bool
     */
    public function deleteEventIcon($eventIcon, $user) {
        DB::beginTransaction();

        try {
            $this->deleteImage($eventIcon->imagePath, $eventIcon->imageFileName);
            $eventIcon->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

}
