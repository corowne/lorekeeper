<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use App\Models\SitePage;
use App\Models\SitePageCategory;
use App\Models\SitePageSection;

class PageService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Page Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of site pages.
    |
    */

    /**
     * Creates a site page.
     *
     * @param  array                  $data
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\SitePage
     */
    public function createPage($data, $user)
    {
        DB::beginTransaction();

        try {
            if(isset($data['text']) && $data['text']) $data['parsed_text'] = parse($data['text']);
            $data['user_id'] = $user->id;
            if(!isset($data['is_visible'])) $data['is_visible'] = 0;
            if(!isset($data['can_comment'])) $data['can_comment'] = 0;

            $page = SitePage::create($data);

            return $this->commitReturn($page);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Updates a site page.
     *
     * @param  \App\Models\SitePage   $news
     * @param  array                  $data 
     * @param  \App\Models\User\User  $user
     * @return bool|\App\Models\SitePage
     */
    public function updatePage($page, $data, $user)
    {
        DB::beginTransaction();

        try {
            // More specific validation
            if(SitePage::where('key', $data['key'])->where('id', '!=', $page->id)->exists()) throw new \Exception("The key has already been taken.");

            if(isset($data['text']) && $data['text']) $data['parsed_text'] = parse($data['text']);
            $data['user_id'] = $user->id;
            if(!isset($data['is_visible'])) $data['is_visible'] = 0;
            if(!isset($data['can_comment'])) $data['can_comment'] = 0;

            $page->update($data);

            return $this->commitReturn($page);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a site page.
     *
     * @param  \App\Models\SitePage  $news
     * @return bool
     */
    public function deletePage($page)
    {
        DB::beginTransaction();

        try {
            // Specific pages such as the TOS/privacy policy cannot be deleted from the admin panel.
            if(Config::get('lorekeeper.text_pages.'.$page->key)) throw new \Exception("You cannot delete this page.");

            $page->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**********************************************************************************************
     
        PAGE CATEGORIES

    **********************************************************************************************/

    /**
     * Create a category.
     *
     * @param  array                 $data
     * @return \App\Models\SitePageCategory|bool
     */
    public function createPageCategory($data)
    {
        DB::beginTransaction();

        try {
            $data = $this->populateCategoryData($data);

            $category = SitePageCategory::create($data);

            return $this->commitReturn($category);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Update a category.
     *
     * @param  \App\Models\SitePageCategory         $category
     * @param  array                                $data
     * @return \App\Models\SitePageCategory|bool
     */
    public function updatePageCategory($category, $data)
    {
        DB::beginTransaction();

        try {
            // More specific validation
            if(SitePageCategory::where('name', $data['name'])->where('id', '!=', $category->id)->exists()) throw new \Exception("The name has already been taken.");

            $data = $this->populateCategoryData($data, $category);

            $image = null;            
            if(isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $image = $data['image'];
                unset($data['image']);
            }

            $category->update($data);

            if ($category) $this->handleImage($image, $category->categoryImagePath, $category->categoryImageFileName);

            return $this->commitReturn($category);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Handle category data.
     *
     * @param  array                                     $data
     * @param  \App\Models\SitePageCategory|null  $category
     * @return array
     */
    private function populateCategoryData($data, $category = null)
    {
        if(isset($data['description']) && $data['description']) $data['parsed_description'] = parse($data['description']);
        
        if(isset($data['remove_image']))
        {
            if($category && $category->has_image && $data['remove_image']) 
            { 
                $data['has_image'] = 0; 
                $this->deleteImage($category->categoryImagePath, $category->categoryImageFileName); 
            }
            unset($data['remove_image']);
        }

        return $data;
    }

    /**
     * Delete a category.
     *
     * @param  \App\Models\SitePageCategory  $category
     * @return bool
     */
    public function deletePageCategory($category)
    {
        DB::beginTransaction();

        try {
            // Check first if the category is currently in use
            if(SitePage::where('page_category_id', $category->id)->exists()) throw new \Exception("A page with this category exists. Please change its category first.");
            
            if($category->has_image) $this->deleteImage($category->categoryImagePath, $category->categoryImageFileName); 
            $category->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Sorts category order.
     *
     * @param  array  $data
     * @return bool
     */
    public function sortPageCategory($data)
    {
        DB::beginTransaction();

        try {
            // explode the sort array and reverse it since the order is inverted
            $sort = array_reverse(explode(',', $data));

            foreach($sort as $key => $s) {
                SitePageCategory::where('id', $s)->update(['sort' => $key]);
            }

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**********************************************************************************************
     
        PAGE SECTIONS

    **********************************************************************************************/

    /**
     * Create a section.
     *
     * @param  array                 $data
     * @param  array                 $contents
     * @return \App\Models\SitePageSection|bool
     */
    public function createPageSection($data, $contents)
    {
        DB::beginTransaction();

        try {
            $section = SitePageSection::create($data);

            //update categories
            if(isset($contents['categories']) && $contents['categories'])
                SitePageCategory::whereIn('id', $contents['categories'])->update(array('section_id' => $section->id));

            return $this->commitReturn($section);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Update a section.
     *
     * @param  \App\Models\SitePageSection         $section
     * @param  array                                $data
     * @param  array                 $contents
     * @return \App\Models\SitePageSection|bool
     */
    public function updatePageSection($section, $data, $contents)
    {
        DB::beginTransaction();

        try {
            // More specific validation
            if(SitePageSection::where('name', $data['name'])->where('id', '!=', $section->id)->exists()) throw new \Exception("The name has already been taken.");

            $section->update($data);

            SitePageCategory::where('section_id', $section->id)->update(array('section_id' => 0));
            if(isset($contents['categories']))
                SitePageCategory::whereIn('id', $contents['categories'])->update(array('section_id' => $section->id));

            return $this->commitReturn($section);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Delete a section.
     *
     * @param  \App\Models\SitePageSection  $section
     * @return bool
     */
    public function deletePageSection($section)
    {
        DB::beginTransaction();

        try {
            // Check first if the section is currently in use
            SitePageCategory::where('section_id', $section->id)->update(array('section_id' => 0));
            
            $section->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Sorts section order.
     *
     * @param  array  $data
     * @return bool
     */
    public function sortPageSection($data)
    {
        DB::beginTransaction();

        try {
            // explode the sort array and reverse it since the order is inverted
            $sort = array_reverse(explode(',', $data));

            foreach($sort as $key => $s) {
                SitePageSection::where('id', $s)->update(['sort' => $key]);
            }

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

}