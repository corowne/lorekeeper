<?php namespace App\Services;

use App\Services\Service;

use Carbon\Carbon;

use DB;
use Config;
use Image;
use Notifications;

use App\Models\User\User;
use App\Models\Character\Character;
use App\Models\Character\CharacterFeature;
use App\Models\Character\CharacterImage;
use App\Models\Character\CharacterLog;

class CharacterManager extends Service
{
    public function pullNumber($categoryId)
    {
        $digits = Config::get('lorekeeper.settings.character_number_digits');
        $result = str_pad('', $digits, '0'); // A default value, in case
        $number = 0;

        // First check if the number needs to be the overall next
        // or next in category, and retrieve the highest number
        if(Config::get('lorekeeper.settings.character_pull_number') == 'all')
        {
            $character = Character::orderBy('number', 'DESC')->first();
            if($character) $number = ltrim($character->number, 0);
            if(!strlen($number)) $number = '0';
        }
        else if (Config::get('lorekeeper.settings.character_pull_number') == 'category' && $categoryId)
        {
            $character = Character::where('character_category_id', $categoryId)->orderBy('number', 'DESC')->first();
            if($character) $number = ltrim($character->number, 0);
            if(!strlen($number)) $number = '0';
        }

        $result = sprintf('%0'.$digits.'d', $number + 1);
        
        return $result;
    }

    public function createCharacter($data, $user)
    {
        DB::beginTransaction();

        try {
            // Get owner info
            $recipient = null;
            $recipientId = null;
            $alias = null;
            if(isset($data['user_id']) && $data['user_id']) $recipient = User::find($data['user_id']);
            elseif(isset($data['owner_alias']) && $data['owner_alias']) $recipient = User::where('alias', $data['owner_alias'])->first();

            if($recipient) {
                $recipientId = $recipient->id;
                $data['user_id'] = $recipient->id;
            }
            else {
                $alias = $data['owner_alias'];
            }

            // Create character
            $character = $this->handleCharacter($data);
            if(!$character) throw new \Exception("Error happened while trying to create character.");

            // Create character image
            $image = $this->handleCharacterImage($data, $character);
            if(!$image) throw new \Exception("Error happened while trying to create image.");
            
            // Update the character's image ID
            $character->character_image_id = $image->id;
            $character->save();
            
            // Add a log for the character
            $this->createLog($user->id, $recipientId, $alias, $character->id, 'Character Created', 'Initial upload', 'character');

            // Add a log for the user
            $this->createLog($user->id, $recipientId, $alias, $character->id, 'Character Created', 'Initial upload', 'user');

            // If the recipient has an account, send them a notification
            if($recipient && $user->id != $recipient->id) {
                Notifications::create('CHARACTER_UPLOAD', $recipient, [
                    'character_url' => $character->url,
                    'character_slug' => $character->slug,
                ]);
            }

            return $this->commitReturn($character);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    private function handleCharacter($data)
    {
        try {
            $characterData = array_only($data, [
                'character_category_id', 'rarity_id', 'user_id',
                'number', 'slug', 'description',
                'sale_value', 'transferrable_at', 'is_visible'
            ]);
            $characterData['owner_alias'] = isset($characterData['user_id']) ? null : $data['owner_alias'];
            $characterData['is_sellable'] = isset($data['is_sellable']);
            $characterData['is_tradeable'] = isset($data['is_tradeable']);
            $characterData['is_giftable'] = isset($data['is_giftable']);
            $characterData['is_visible'] = isset($data['is_visible']);
            $characterData['sale_value'] = isset($data['sale_value']) ? $data['sale_value'] : 0;
            $characterData['is_gift_art_allowed'] = 0;
            $characterData['is_trading'] = 0;
            $characterData['parsed_description'] = parse($data['description']);
            
            $character = Character::create($characterData);

            // Create character profile row
            $character->profile()->create([]);

            return $character;
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return false;
    }

    private function handleCharacterImage($data, $character)
    {
        try {
            $imageData = array_only($data, [
                'species_id', 'rarity_id', 'use_cropper', 
                'x0', 'x1', 'y0', 'y1',
            ]);
            $imageData['use_cropper'] = isset($data['use_cropper']);
            $imageData['description'] = isset($data['image_description']) ? $data['image_description'] : null;
            $imageData['parsed_description'] = parse($imageData['description']);
            $imageData['hash'] = randomString(10);
            $imageData['sort'] = 0;
            $imageData['is_valid'] = 1;
            $imageData['extension'] = $data['image']->getClientOriginalExtension();
            $imageData['character_id'] = $character->id;

            $image = CharacterImage::create($imageData);

            // Attach artists/designers
            foreach($data['designer_alias'] as $key => $alias) {
                if($alias || $data['designer_url'][$key])
                    DB::table('character_image_creators')->insert([
                        'character_image_id' => $image->id,
                        'type' => 'Designer',
                        'url' => $data['designer_url'][$key],
                        'alias' => $alias
                    ]);
            }
            foreach($data['artist_alias'] as $key => $alias) {
                if($alias || $data['artist_url'][$key])
                    DB::table('character_image_creators')->insert([
                        'character_image_id' => $image->id,
                        'type' => 'Artist',
                        'url' => $data['artist_url'][$key],
                        'alias' => $alias
                    ]);
            }

            // Save image
            $this->handleImage($data['image'], $image->imageDirectory, $image->imageFileName);
            
            // Save thumbnail
            if(isset($data['use_cropper'])) $this->cropThumbnail($data['image'], array_only($data, ['x0','x1','y0','y1']), $image);
            else $this->handleImage($data['thumbnail'], $image->thumbnailDirectory, $image->thumbnailFileName);

            // Attach features
            foreach($data['feature_id'] as $key => $featureId) {
                if($featureId) {
                    $feature = CharacterFeature::create(['character_image_id' => $image->id, 'feature_id' => $featureId, 'data' => $data['feature_data'][$key]]);
                }
            }

            return $image;
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return false;

    }

    private function cropThumbnail($image, $points, $characterImage)
    {
        $cropWidth = $points['x1'] - $points['x0'];
        $cropHeight = $points['y1'] - $points['y0'];

        $image = Image::make($characterImage->imagePath . '/' . $characterImage->imageFileName);
        
        // Crop according to the selected area
        $image->crop($cropWidth, $cropHeight, $points['x0'], $points['y0']);

        // Resize to fit the thumbnail size
        $image->resize(Config::get('lorekeeper.settings.masterlist_thumbnails.width'), Config::get('lorekeeper.settings.masterlist_thumbnails.height'));

        // Save the thumbnail
        $image->save($characterImage->thumbnailPath . '/' . $characterImage->thumbnailFileName);
        
    }
    
    

    public function createLog($senderId, $recipientId, $recipientAlias, $characterId, $type, $data, $logType)
    {
        return DB::table($logType == 'character' ? 'character_log' : 'user_character_log')->insert(
            [
                'sender_id' => $senderId,
                'recipient_id' => $recipientId,
                'recipient_alias' => $recipientAlias,
                'character_id' => $characterId,
                'log' => $type . ($data ? ' (' . $data . ')' : ''),
                'log_type' => $type,
                'data' => $data, 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        );
    }

}