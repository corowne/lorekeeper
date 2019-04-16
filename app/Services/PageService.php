<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use App\Models\SitePage;

class PageService extends Service
{

    public function createPage($data, $user)
    {
        DB::beginTransaction();

        try {
            if(isset($data['text']) && $data['text']) $data['parsed_text'] = parse($data['text']);

            $page = SitePage::create($data);

            return $this->commitReturn($page);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    public function updatePage($page, $data, $user)
    {
        DB::beginTransaction();

        try {
            // More specific validation
            if(Page::where('key', $data['key'])->where('id', '!=', $page->id)->exists()) throw new \Exception("The key has already been taken.");

            if(isset($data['text']) && $data['text']) $data['parsed_text'] = parse($data['text']);

            $page->update($data);

            return $this->commitReturn($page);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    public function deletePage($page)
    {
        DB::beginTransaction();

        try {
            $page->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
}