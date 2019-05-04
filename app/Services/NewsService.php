<?php namespace App\Services;

use App\Services\Service;

use DB;
use Config;

use App\Models\User\User;
use App\Models\News;

class NewsService extends Service
{

    public function createNews($data, $user)
    {
        DB::beginTransaction();

        try {
            $data['parsed_text'] = parse($data['text']);
            $data['user_id'] = $user->id;
            if(!isset($data['is_visible'])) $data['is_visible'] = 0;

            $news = News::create($data);

            if($news->is_visible) $this->alertUsers();

            return $this->commitReturn($news);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    public function updateNews($news, $data, $user)
    {
        DB::beginTransaction();

        try {
            // More specific validation
            if(News::where('key', $data['key'])->where('id', '!=', $news->id)->exists()) throw new \Exception("The key has already been taken.");

            $data['parsed_text'] = parse($data['text']);
            $data['user_id'] = $user->id;
            if(!isset($data['is_visible'])) $data['is_visible'] = 0;

            $news->update($data);

            return $this->commitReturn($news);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    public function deleteNews($news)
    {
        DB::beginTransaction();

        try {
            $news->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    public function updateQueue()
    {
        $count = News::shouldBeVisible()->count();
        if($count) {
            DB::beginTransaction();

            try {
                News::shouldBeVisible()->update(['is_visible' => 1]);
                $this->alertUsers();

                return $this->commitReturn(true);
            } catch(\Exception $e) { 
                $this->setError('error', $e->getMessage());
            }
            return $this->rollbackReturn(false);
        }
    }

    private function alertUsers()
    {
        User::query()->update(['is_news_unread' => 1]);
        return true;
    }
}