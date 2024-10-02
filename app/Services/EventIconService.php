<?php

namespace App\Services;

use App\Models\EventIcon\EventIcon;
use Illuminate\Support\Facades\DB;
use Log;

class EventIconService extends Service {
    /*
    |--------------------------------------------------------------------------
    | EventIcon Service
    |--------------------------------------------------------------------------
    |
    | Handles uploading and manipulation of event icon files.
    |
    */

    /**
     * Creates an event icon.
     *
     * @param mixed $data
     * @param mixed $user
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

            $eventIcon = EventIcon::create($data);

            // if (!$this->logAdminAction($user, 'Created Event Icon', 'Created '.$eventIcon->link)) {
            //     throw new \Exception('Failed to log admin action.');
            // }

            if ($image) {
                $this->handleImage($image, $eventIcon->imagePath, $image->getClientOriginalName(), null);
            }

            return $this->commitReturn($eventIcon);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

        /**
     * Updates a eventIcon.
     *
     * @param \App\Models\EventIcon\EventIcon $shop
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return \App\Models\EventIcon\EventIcon|bool
     */
    public function updateEventIcon($eventIcon, $data, $user) {
        DB::beginTransaction();

        try {
            $image = null;
            if (isset($data['image']) && $data['image']) {
                $image = $data['image'];
                unset($data['image']);
                $data['image'] = $image->getClientOriginalName();
            } else {
                unset($data['image']);
            }

            if (!isset($data['is_visible'])) {
                $data['is_visible'] = 0;
            }

            $eventIcon->update($data);

            // if (!$this->logAdminAction($user, 'Updated event icon', 'Created '.$eventIcon->link)) {
            //     throw new \Exception('Failed to log admin action.');
            // }

            if ($image) {
                $this->handleImage($image, $eventIcon->imagePath, $image->getClientOriginalName(), null);
            }

            return $this->commitReturn($eventIcon);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a file.
     *
     * @param mixed $eventIcon
     * @param mixed $user
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

        /**
     * Sorts eventIcon order.
     *
     * @param array $data
     *
     * @return bool
     */
    public function sortEventIcon($data) {
        DB::beginTransaction();

        try {
            // explode the sort array and reverse it since the order is inverted
            $sort = array_reverse(explode(',', $data));

            foreach ($sort as $key => $s) {
                EventIcon::where('id', $s)->update(['sort' => $key]);
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }
}
