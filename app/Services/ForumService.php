<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;
use SoftDeletes;

use App\Models\Forum;

class ForumService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Forum Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of site forums.
    |
    */

    /**
     * Creates a site forum.
     *
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\Forum
     */
    public function createForum($data, $user)
    {
        DB::beginTransaction();

        try {

            $data = $this->populateData($data);

            $image = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }
            else $data['has_image'] = 0;

            $forum = Forum::create($data);

            if ($image) {
                $forum->extension = $image->getClientOriginalExtension();
                $forum->update();
                $this->handleImage($image, $forum->imagePath, $forum->imageFileName, null);
            }


            return $this->commitReturn($forum);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates a site forum.
     *
     * @param  \App\Models\Forum        $forum
     * @param  array                    $data
     * @param  \App\Models\User\User    $user
     * @return bool|\App\Models\Forum
     */
    public function updateForum($forum, $data, $user)
    {
        DB::beginTransaction();

        try {


            $image = null;
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }

            $data = $this->populateData($data, $forum);

            $forum->update($data);

            if ($image) {
                $forum->extension = $image->getClientOriginalExtension();
                $forum->update();
                $this->handleImage($image, $forum->imagePath, $forum->imageFileName, null);
            }

            return $this->commitReturn($forum);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }



    /**
     * Processes user input for creating/updating a forum.
     *
     * @param  array                  $data
     * @param  \App\Models\Item\Item  $forum
     * @return array
     */
    private function populateData($data, $forum = null)
    {
        (isset($data['description']) && $data['description']) ? $data['description'] : $data['description']  = null;
        if(isset($data['description']) && $data['description']) $data['parsed_description'] = parse($data['description']);
        else $data['parsed_description'] = null;

        if(!isset($data['is_active'])) $data['is_active'] = 0;
        if(!isset($data['is_locked'])) $data['is_locked'] = 0;
        if(!isset($data['sort'])) $data['sort'] = 0;
        if(!isset($data['staff_only'])) $data['staff_only'] = 0;
        if(!isset($data['role_limit'])) $data['role_limit'] = null;
        if(!isset($data['parent_id'])) $data['parent_id'] = null;

        if(isset($data['remove_image']) && $data['remove_image'])
        {
            if($forum && $forum->has_image && $data['remove_image'])
            {
                $data['has_image'] = 0;
                $this->deleteImage($forum->imagePath, $forum->imageFileName);
            }
            $data['extension'] = null;
            unset($data['remove_image']);
        }


        return $data;
    }



    /**
     * Deletes a site forum.
     *
     * @param  \App\Models\Forum  $forum
     * @return bool
     */
    public function deleteForum($forum, $data)
    {
        DB::beginTransaction();

        try {

            if(isset($forum->extension)) $this->deleteImage($forum->imagePath, $forum->imageFileName);
            if(isset($data['child_boards']) && $data['child_boards'])
            {
                if(!$this->recursiveDeletion($forum)) throw new \Exception("Could not delete children.");
            }
            else {
                foreach($forum->children as $child)
                {
                    $child->update(['parent_id' => $forum->parent_id]);
                }
            }

            $forum->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Recursively delete all children
     *
     */
    private function recursiveDeletion($forum)
    {
        try
        {
            if(count($forum->children))
            {
                foreach($forum->children as $board)
                {
                    if(isset($forum->extension)) $this->deleteImage($forum->imagePath, $forum->imageFileName);
                    $this->recursiveDeletion($board);
                    $forum->delete();
                    return true;
                }
            }
            else
            {
                if(isset($forum->extension)) $this->deleteImage($forum->imagePath, $forum->imageFileName);
                $forum->delete();
                return true;
            }
        }
        catch(\Exception $e) {
            return false;
        }
        return false;
    }


}
