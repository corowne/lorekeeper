<?php namespace App\Services;

use App\Services\Service;

use Carbon\Carbon;

use DB;
use Config;
use Image;
use Notifications;
use Settings;

use App\Models\User\User;
use App\Models\Character\Character;
use App\Models\Character\CharacterCategory;
use App\Models\Character\CharacterFeature;
use App\Models\Character\CharacterImage;
use App\Models\Character\CharacterTransfer;
use App\Models\User\UserCharacterLog;

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
            $character = Character::myo(0)->orderBy('number', 'DESC')->first();
            if($character) $number = ltrim($character->number, 0);
            if(!strlen($number)) $number = '0';
        }
        else if (Config::get('lorekeeper.settings.character_pull_number') == 'category' && $categoryId)
        {
            $character = Character::myo(0)->where('character_category_id', $categoryId)->orderBy('number', 'DESC')->first();
            if($character) $number = ltrim($character->number, 0);
            if(!strlen($number)) $number = '0';
        }

        $result = format_masterlist_number($number + 1, $digits);
        
        return $result;
    }

    public function createCharacter($data, $user, $isMyo = false)
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
            $character = $this->handleCharacter($data, $isMyo);
            if(!$character) throw new \Exception("Error happened while trying to create character.");

            // Create character image
            $data['is_valid'] = true; // New image of new characters are always valid
            $image = $this->handleCharacterImage($data, $character, $isMyo);
            if(!$image) throw new \Exception("Error happened while trying to create image.");
            
            // Update the character's image ID
            $character->character_image_id = $image->id;
            $character->save();
            
            // Add a log for the character
            // This logs all the updates made to the character
            $this->createLog($user->id, $recipientId, $alias, $character->id, $isMyo ? 'MYO Slot Created' : 'Character Created', 'Initial upload', 'character');

            // Add a log for the user
            // This logs ownership of the character
            $this->createLog($user->id, $recipientId, $alias, $character->id, $isMyo ? 'MYO Slot Created' : 'Character Created', 'Initial upload', 'user');

            // Update the user's FTO status and character count
            if($recipient) {
                if(!$isMyo) {
                    $recipient->settings->is_fto = 0; // MYO slots don't affect the FTO status - YMMV
                    $recipient->settings->character_count++;
                }
                else $recipient->settings->myo_slot_count++;
                $recipient->settings->save();
            }


            // If the recipient has an account, send them a notification
            if($recipient && $user->id != $recipient->id) {
                Notifications::create($isMyo ? 'MYO_GRANT' : 'CHARACTER_UPLOAD', $recipient, [
                    'character_url' => $character->url,
                ] + ($isMyo ? 
                    ['name' => $character->name] :
                    ['character_slug' => $character->slug]
                ));
            }

            return $this->commitReturn($character);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    private function handleCharacter($data, $isMyo = false)
    {
        try {
            if($isMyo)
            {
                $data['character_category_id'] = null;
                $data['number'] = null;
                $data['slug'] = null;
            }

            $characterData = array_only($data, [
                'character_category_id', 'rarity_id', 'user_id',
                'number', 'slug', 'description',
                'sale_value', 'transferrable_at', 'is_visible'
            ] + ($isMyo ? ['name'] : []));
            $characterData['owner_alias'] = isset($characterData['user_id']) ? null : $data['owner_alias'];
            $characterData['is_sellable'] = isset($data['is_sellable']);
            $characterData['is_tradeable'] = isset($data['is_tradeable']);
            $characterData['is_giftable'] = isset($data['is_giftable']);
            $characterData['is_visible'] = isset($data['is_visible']);
            $characterData['sale_value'] = isset($data['sale_value']) ? $data['sale_value'] : 0;
            $characterData['is_gift_art_allowed'] = 0;
            $characterData['is_trading'] = 0;
            $characterData['parsed_description'] = parse($data['description']);
            if($isMyo) $characterData['is_myo_slot'] = 1;
            
            $character = Character::create($characterData);

            // Create character profile row
            $character->profile()->create([]);

            return $character;
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return false;
    }

    private function handleCharacterImage($data, $character, $isMyo = false)
    {
        try {
            if($isMyo)
            {
                $data['species_id'] = (isset($data['species_id']) && $data['species_id']) ? $data['species_id'] : null;
                $data['rarity_id'] = (isset($data['rarity_id']) && $data['rarity_id']) ? $data['rarity_id'] : null;
                

                // Use default images for MYO slots without an image provided
                if(!isset($data['image']))
                {
                    $data['image'] = asset('images/myo.png');
                    $data['thumbnail'] = asset('images/myo-th.png');
                    $data['extension'] = 'png';
                    $data['default_image'] = true;
                    unset($data['use_cropper']);
                }
            }
            $imageData = array_only($data, [
                'species_id', 'rarity_id', 'use_cropper', 
                'x0', 'x1', 'y0', 'y1',
            ]);
            $imageData['use_cropper'] = isset($data['use_cropper']) ;
            $imageData['description'] = isset($data['image_description']) ? $data['image_description'] : null;
            $imageData['parsed_description'] = parse($imageData['description']);
            $imageData['hash'] = randomString(10);
            $imageData['sort'] = 0;
            $imageData['is_valid'] = isset($data['is_valid']);
            $imageData['is_visible'] = isset($data['is_visible']);
            $imageData['extension'] = isset($data['extension']) ? $data['extension'] : $data['image']->getClientOriginalExtension();
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
            $this->handleImage($data['image'], $image->imageDirectory, $image->imageFileName, null, isset($data['default_image']));
            
            // Save thumbnail
            if(isset($data['use_cropper'])) $this->cropThumbnail($data['image'], array_only($data, ['x0','x1','y0','y1']), $image);
            else $this->handleImage($data['thumbnail'], $image->imageDirectory, $image->thumbnailFileName, null, isset($data['default_image']));
            
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

    public function createLog($senderId, $recipientId, $recipientAlias, $characterId, $type, $data, $logType, $isUpdate = false, $oldData = null, $newData = null)
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
            ] + ($logType == 'character' ? 
                [
                    'change_log' => $isUpdate ? json_encode([
                        'old' => $oldData,
                        'new' => $newData
                    ]) : null
                ] : [])
        );
    }

    public function createImage($data, $character, $user)
    {
        DB::beginTransaction();

        try {
            $data['is_visible'] = 1;

            // Create character image
            $image = $this->handleCharacterImage($data, $character);
            if(!$image) throw new \Exception("Error happened while trying to create image.");
            
            // Update the character's image ID
            $character->character_image_id = $image->id;
            $character->save();
            
            // Add a log for the character
            // This logs all the updates made to the character
            $this->createLog($user->id, $character->user_id, $character->user->alias, $character->id, 'Character Image Uploaded', '[#'.$image->id.']', 'character');

            // If the recipient has an account, send them a notification
            if($character->user && $user->id != $character->user_id) {
                Notifications::create('IMAGE_UPLOAD', $character->user, [
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

    public function updateImageFeatures($data, $image, $user)
    {
        DB::beginTransaction();

        try {
            // Log old features
            $old = [];
            $old['features'] = $this->generateFeatureList($image);
            $old['species'] = $image->species->displayName;
            $old['rarity'] = $image->rarity->displayName;

            // Clear old features
            $image->features()->delete();

            // Attach features
            foreach($data['feature_id'] as $key => $featureId) {
                if($featureId) {
                    $feature = CharacterFeature::create(['character_image_id' => $image->id, 'feature_id' => $featureId, 'data' => $data['feature_data'][$key]]);
                }
            }

            // Update other stats
            $image->species_id = $data['species_id'];
            $image->rarity_id = $data['rarity_id'];
            $image->save();

            $new = [];
            $new['features'] = $this->generateFeatureList($image);
            $new['species'] = $image->species->displayName;
            $new['rarity'] = $image->rarity->displayName;
            
            // Add a log for the character
            // This logs all the updates made to the character
            $this->createLog($user->id, null, null, $image->character_id, 'Traits Updated', '#'.$image->id, 'character', true, $old, $new);

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    private function generateFeatureList($image)
    {
        $result = '';
        foreach($image->features as $feature)
            $result .= '<div><strong>' . $feature->feature->category->displayName . ':</strong> ' . $feature->feature->displayName . '</div>';
        return $result;
    }
    
    public function updateImageNotes($data, $image, $user)
    {
        DB::beginTransaction();

        try {
            $old = $image->parsed_description;

            // Update the image's notes
            $image->description = $data['description'];
            $image->parsed_description = parse($data['description']);
            $image->save();
            
            // Add a log for the character
            // This logs all the updates made to the character
            $this->createLog($user->id, null, null, $image->character_id, 'Image Notes Updated', '[#'.$image->id.']', 'character', true, $old, $image->parsed_description);

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    public function updateImageCredits($data, $image, $user)
    {
        DB::beginTransaction();

        try {
            $old = $this->generateCredits($image);

            // Clear old artists/designers
            $image->creators()->delete();

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
            
            // Add a log for the character
            // This logs all the updates made to the character
            $this->createLog($user->id, null, null, $image->character_id, 'Image Credits Updated', '[#'.$image->id.']', 'character', true, $old, $this->generateCredits($image));

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    private function generateCredits($image)
    {
        $result = ['designers' => '', 'artists' => ''];
        foreach($image->designers as $designer)
            $result['designers'] .= '<div>' . $designer->displayLink() . '</div>';
        foreach($image->artists as $artist)
            $result['artists'] .= '<div>' . $artist->displayLink() . '</div>';
        return $result;
    }
    
    public function reuploadImage($data, $image, $user)
    {
        DB::beginTransaction();

        try {
            // Save image
            $this->handleImage($data['image'], $image->imageDirectory, $image->imageFileName);
            
            // Save thumbnail
            if(isset($data['use_cropper'])) $this->cropThumbnail($data['image'], array_only($data, ['x0','x1','y0','y1']), $image);
            else $this->handleImage($data['thumbnail'], $image->thumbnailDirectory, $image->thumbnailFileName);
            
            // Add a log for the character
            // This logs all the updates made to the character
            $this->createLog($user->id, null, null, $image->character_id, 'Image Reuploaded', '[#'.$image->id.']', 'character');

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    public function deleteImage($image, $user)
    {
        DB::beginTransaction();

        try {
            if($image->character->character_image_id == $image->id) throw new \Exception("Cannot delete a character's active image.");

            $image->delete();

            // Delete the image files
            unlink($image->imagePath . '/' . $image->imageFileName);
            unlink($image->imagePath . '/' . $image->thumbnailFileName);
            
            // Add a log for the character
            // This logs all the updates made to the character
            $this->createLog($user->id, null, null, $image->character_id, 'Image Deleted', '[#'.$image->id.']', 'character');

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    public function updateImageSettings($data, $image, $user)
    {
        DB::beginTransaction();

        try {
            if($image->character->character_image_id == $image->id && !isset($data['is_visible'])) throw new \Exception("Cannot hide a character's active image.");

            $image->is_valid = isset($data['is_valid']);
            $image->is_visible = isset($data['is_visible']);
            $image->save();
            
            // Add a log for the character
            // This logs all the updates made to the character
            $this->createLog($user->id, null, null, $image->character_id, 'Image Visibility/Validity Updated', '[#'.$image->id.']', 'character');

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    public function updateActiveImage($image, $user)
    {
        DB::beginTransaction();

        try {
            if($image->character->character_image_id == $image->id) return true;
            if(!$image->is_visible) throw new \Exception("Cannot set a non-visible image as the character's active image.");

            $image->character->character_image_id = $image->id;
            $image->character->save();
            
            // Add a log for the character
            // This logs all the updates made to the character
            $this->createLog($user->id, null, null, $image->character_id, 'Active Image Updated', '[#'.$image->id.']', 'character');

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    public function sortImages($data, $character, $user)
    {
        DB::beginTransaction();

        try {
            $ids = explode(',', $data['sort']);
            $images = CharacterImage::whereIn('id', $ids)->where('character_id', $character->id)->orderByRaw(DB::raw('FIELD(id, '.implode(',', $ids).')'))->get();
            
            if(count($images) != count($ids)) throw new \Exception("Invalid image included in sorting order.");
            if(!$images->first()->is_visible) throw new \Exception("Cannot set a non-visible image as the character's active image.");

            $count = 0;
            foreach($images as $image)
            {
                //if($count == 1) 
                //{
                //    // Set the first one as the active image
                //    $image->character->image_id = $image->id;
                //    $image->character->save();
                //}
                $image->sort = $count;
                $image->save();
                $count++;
            }
            
            // Add a log for the character
            // This logs all the updates made to the character
            $this->createLog($user->id, null, null, $image->character_id, 'Image Order Updated', '', 'character');

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    public function sortCharacters($data, $user)
    {
        DB::beginTransaction();

        try {
            $ids = array_reverse(explode(',', $data['sort'])); 
            $characters = Character::myo(0)->whereIn('id', $ids)->where('user_id', $user->id)->where('is_visible', 1)->orderByRaw(DB::raw('FIELD(id, '.implode(',', $ids).')'))->get();
            
            if(count($characters) != count($ids)) throw new \Exception("Invalid character included in sorting order.");

            $count = 0;
            foreach($characters as $character)
            {
                $character->sort = $count;
                $character->save();
                $count++;
            }

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    public function updateCharacterStats($data, $character, $user)
    {
        DB::beginTransaction();

        try {
            if(Character::where('slug', $data['slug'])->where('id', '!=', $character->id)->exists()) throw new \Exception("Character code must be unique.");

            $characterData = array_only($data, [
                'character_category_id', 
                'number', 'slug', 
            ]);
            $characterData['is_sellable'] = isset($data['is_sellable']);
            $characterData['is_tradeable'] = isset($data['is_tradeable']);
            $characterData['is_giftable'] = isset($data['is_giftable']);
            $characterData['sale_value'] = isset($data['sale_value']) ? $data['sale_value'] : 0;
            $characterData['transferrable_at'] = isset($data['transferrable_at']) ? $data['transferrable_at'] : null;

            // Needs to be cleaned up
            $result = [];
            $old = [];
            $new = [];
            if($characterData['character_category_id'] != $character->character_category_id) {
                $result[] = 'character category';
                $old['character_category'] = $character->category->displayName;
                $new['character_category'] = CharacterCategory::find($characterData['character_category_id'])->displayName;
            }
            if($characterData['number'] != $character->number) {
                $result[] = 'character number';
                $old['number'] = $character->number;
                $new['number'] = $characterData['number'];
            }
            if($characterData['slug'] != $character->number) {
                $result[] = 'character code';
                $old['slug'] = $character->slug;
                $new['slug'] = $characterData['slug'];
            }
            if($characterData['is_sellable'] != $character->is_sellable) {
                $result[] = 'sellable status';
                $old['is_sellable'] = $character->is_sellable;
                $new['is_sellable'] = $characterData['is_sellable'];
            }
            if($characterData['is_tradeable'] != $character->is_tradeable) {
                $result[] = 'tradeable status';
                $old['is_tradeable'] = $character->is_tradeable;
                $new['is_tradeable'] = $characterData['is_tradeable'];
            }
            if($characterData['is_giftable'] != $character->is_giftable) {
                $result[] = 'giftable status';
                $old['is_giftable'] = $character->is_giftable;
                $new['is_giftable'] = $characterData['is_giftable'];
            }
            if($characterData['sale_value'] != $character->sale_value) {
                $result[] = 'sale value';
                $old['sale_value'] = $character->sale_value;
                $new['sale_value'] = $characterData['sale_value'];
            }
            if($characterData['transferrable_at'] != $character->transferrable_at) {
                $result[] = 'transfer cooldown';
                $old['transferrable_at'] = $character->transferrable_at;
                $new['transferrable_at'] = $characterData['transferrable_at'];
            }
            
            if(count($result)) 
            {
                $character->update($characterData);
                
                // Add a log for the character
                // This logs all the updates made to the character
                $this->createLog($user->id, null, null, $character->id, 'Character Updated', ucfirst(implode(', ', $result)) . ' edited', 'character', true, $old, $new);
            }

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    public function updateCharacterDescription($data, $character, $user)
    {
        DB::beginTransaction();

        try {
            $old = $character->parsed_description;

            // Update the image's notes
            $character->description = $data['description'];
            $character->parsed_description = parse($data['description']);
            $character->save();
                
            // Add a log for the character
            // This logs all the updates made to the character
            $this->createLog($user->id, null, null, $character->id, 'Character Description Updated', '', 'character', true, $old, $character->parsed_description);

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    public function updateCharacterSettings($data, $character, $user)
    {
        DB::beginTransaction();

        try {
            $old = ['is_visible' => $character->is_visible];

            $character->is_visible = isset($data['is_visible']);
            $character->save();
                
            // Add a log for the character
            // This logs all the updates made to the character
            $this->createLog($user->id, null, null, $character->id, 'Character Visibility Updated', '', 'character', true, $old, ['is_visible' => $character->is_visible]);

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    

    public function updateCharacterProfile($data, $character, $user, $isAdmin = false)
    {
        DB::beginTransaction();

        try {
            // Allow updating the gift art/trading options if the editing
            // user owns the character
            if(!$isAdmin)
            {
                if($character->user_id != $user->id) throw new \Exception("You cannot edit this character.");
                $character->is_gift_art_allowed = isset($data['is_gift_art_allowed']);
                $character->is_trading = isset($data['is_trading']);
                $character->save();
            }

            // Update the character's profile
            $character->name = $data['name'];
            $character->save();

            $character->profile->text = $data['text'];
            $character->profile->parsed_text = parse($data['text']);
            $character->profile->save();

            if($isAdmin && isset($data['alert_user']) && $character->is_visible)
            {
                Notifications::create('CHARACTER_PROFILE_EDIT', $character->user, [
                    'character_name' => $character->name,
                    'character_slug' => $character->slug,
                    'sender_url' => $user->url,
                    'sender_name' => $user->name
                ]);
            }

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    public function deleteCharacter($character, $user)
    {
        DB::beginTransaction();

        try {
            // Delete character
            // This is a soft delete, so the character still kind of exists
            $character->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    public function createTransfer($data, $character, $user)
    {
        DB::beginTransaction();

        try {
            if($user->id != $character->user_id) throw new \Exception("You do not own this character.");
            if(!$character->is_sellable && !$character->is_tradeable && !$character->is_giftable) throw new \Exception("This character is not transferrable.");
            if($character->transferrable_at && $character->transferrable_at->isFuture()) throw new \Exception("This character is still on transfer cooldown and cannot be transferred.");
            if(CharacterTransfer::active()->where('character_id', $character->id)->exists()) throw new \Exception("This character is in an active trade.");
            
            $recipient = User::find($data['recipient_id']);
            if(!$recipient) throw new \Exception("Invalid user selected.");
            
            CharacterTransfer::create([
                'character_id' => $character->id, 
                'sender_id' => $user->id, 
                'recipient_id' => $recipient->id, 
                'status' => 'Pending',

                // if the queue is closed, all transfers are auto-approved
                'is_approved' => !Settings::get('open_transfers_queue') 
            ]);

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    public function adminTransfer($data, $character, $user)
    {
        DB::beginTransaction();

        try {
            $recipient = User::find($data['recipient_id']);
            if(!$recipient) throw new \Exception("Invalid user selected.");
            if($character->user_id == $recipient->id) throw new \Exception("Cannot transfer a character to the same user.");

            // If the character is in an active transfer, cancel it
            $transfer = CharacterTransfer::active()->where('character_id', $character->id)->first();
            if($transfer) {
                $transfer->status = 'Canceled';
                $transfer->reason = 'Transfer canceled by '.$user->displayName.' in order to transfer character to another user';
                $transfer->save();
            }

            $sender = $character->user;
            
            $this->moveCharacter($character, $recipient, 'Transferred by ' . $user->displayName . (isset($data['reason']) ? ': ' . $data['reason'] : ''), isset($data['cooldown']) ? $data['cooldown'] : -1);

            // Add notifications for the old and new owners
            Notifications::create('CHARACTER_SENT', $sender, [
                'character_name' => $character->slug,
                'character_slug' => $character->slug,
                'sender_name' => $user->name,
                'sender_url' => $user->url,
                'recipient_name' => $recipient->name,
                'recipient_url' => $recipient->url,
            ]);
            Notifications::create('CHARACTER_RECEIVED', $recipient, [
                'character_name' => $character->slug,
                'character_slug' => $character->slug,
                'sender_name' => $user->name,
                'sender_url' => $user->url,
            ]);

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    public function processTransfer($data, $user)
    {
        DB::beginTransaction();

        try {
            $transfer = CharacterTransfer::where('id', $data['transfer_id'])->active()->where('recipient_id', $user->id)->first();
            if(!$transfer) throw new \Exception("Invalid transfer selected.");

            if($data['action'] == 'Accept') {
                $cooldown = Settings::get('transfer_cooldown');

                $transfer->status = 'Accepted';

                // Process the character move if the transfer has already been approved
                if ($transfer->is_approved) {
                    //check the cooldown saved
                    if(isset($transfer->data['cooldown'])) $cooldown = $transfer->data['cooldown'];
                    $this->moveCharacter($transfer->character, $transfer->recipient, 'User Transfer', $cooldown);
                    if(!Settings::get('open_transfers_queue'))
                        $transfer->data = json_encode([
                            'cooldown' => $cooldown,
                            'staff_id' => null
                        ]);

                    // Notify sender of the successful transfer
                    Notifications::create('CHARACTER_TRANSFER_ACCEPTED', $transfer->sender, [
                        'character_name' => $transfer->character->slug,
                        'character_url' => $transfer->character->url,
                        'sender_name' => $transfer->recipient->name,
                        'sender_url' => $transfer->recipient->url,
                    ]);
                }
            }
            else {
                $transfer->status = 'Rejected';
                $transfer->data = json_encode([
                    'staff_id' => null
                ]);

                // Notify sender that transfer has been rejected
                Notifications::create('CHARACTER_TRANSFER_REJECTED', $transfer->sender, [
                    'character_name' => $transfer->character->slug,
                    'character_url' => $transfer->character->url,
                    'sender_name' => $transfer->recipient->name,
                    'sender_url' => $transfer->recipient->url,
                ]);
            }
            $transfer->save();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    public function cancelTransfer($data, $user)
    {
        DB::beginTransaction();

        try {
            $transfer = CharacterTransfer::where('id', $data['transfer_id'])->active()->where('sender_id', $user->id)->first();
            if(!$transfer) throw new \Exception("Invalid transfer selected.");

            $transfer->status = 'Canceled';
            $transfer->save();
            
            // Notify recipient of the cancelled transfer
            Notifications::create('CHARACTER_TRANSFER_CANCELED', $transfer->recipient, [
                'character_name' => $transfer->character->slug,
                'character_url' => $transfer->character->url,
                'sender_name' => $transfer->sender->name,
                'sender_url' => $transfer->sender->url,
            ]);

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }
    
    public function processTransferQueue($data, $user)
    {
        DB::beginTransaction();

        try {
            $transfer = CharacterTransfer::where('id', $data['transfer_id'])->active()->first();
            if(!$transfer) throw new \Exception("Invalid transfer selected.");
            
            if($data['action'] == 'Approve') {
                $transfer->is_approved = 1;
                $transfer->data = json_encode([
                    'staff_id' => $user->id,
                    'cooldown' => isset($data['cooldown']) ? $data['cooldown'] : Settings::get('transfer_cooldown')
                ]);

                // Process the character move if the recipient has already accepted the transfer
                if($transfer->status == 'Accepted') {
                    $this->moveCharacter($transfer->character, $transfer->recipient, 'User Transfer', isset($data['cooldown']) ? $data['cooldown'] : -1);

                    // Notify both parties of the successful transfer
                    Notifications::create('CHARACTER_TRANSFER_APPROVED', $transfer->sender, [
                        'character_name' => $transfer->character->slug,
                        'character_url' => $transfer->character->url,
                        'sender_name' => $user->name,
                        'sender_url' => $user->url,
                    ]);
                    Notifications::create('CHARACTER_TRANSFER_APPROVED', $transfer->recipient, [
                        'character_name' => $transfer->character->slug,
                        'character_url' => $transfer->character->url,
                        'sender_name' => $user->name,
                        'sender_url' => $user->url,
                    ]);

                }
            }
            else {
                $transfer->status = 'Rejected';
                $transfer->reason = isset($data['reason']) ? $data['reason'] : null;
                $transfer->data = json_encode([
                    'staff_id' => $user->id
                ]);

                // Notify both parties that the request was denied
                Notifications::create('CHARACTER_TRANSFER_DENIED', $transfer->sender, [
                    'character_name' => $transfer->character->slug,
                    'character_url' => $transfer->character->url,
                    'sender_name' => $user->name,
                    'sender_url' => $user->url,
                ]);
                Notifications::create('CHARACTER_TRANSFER_DENIED', $transfer->recipient, [
                    'character_name' => $transfer->character->slug,
                    'character_url' => $transfer->character->url,
                    'sender_name' => $user->name,
                    'sender_url' => $user->url,
                ]);
            }
            $transfer->save();

            return $this->commitReturn(true);
        } catch(\Exception $e) { 
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    private function moveCharacter($character, $recipient, $data, $cooldown = -1)
    {
        $sender = $character->user;

        // Update character counts
        if($character->is_myo_slot) $character->user->settings->myo_slot_count--;
        else $character->user->settings->character_count--;
        $character->user->settings->save();

        if($character->is_myo_slot) $recipient->settings->myo_slot_count++;
        else {
            $recipient->settings->character_count++;
            $recipient->settings->is_fto = 0;
        }
        $recipient->settings->save();

        // Update character owner, sort order and cooldown
        $character->sort = 0;
        $character->user_id = $recipient->id;
        if ($cooldown < 0) {
            // Add the default amount from settings
            $cooldown = Settings::get('transfer_cooldown');
        }
        if($cooldown > 0) {
            if ($character->transferrable_at && $character->transferrable_at->isFuture())
                $character->transferrable_at->addDays($cooldown);
            else $character->transferrable_at = Carbon::now()->addDays($cooldown);
        }
        $character->save();

        // Add a log for the ownership change
        $this->createLog($sender->id, $recipient->id, $recipient->alias, $character->id, $character->is_myo_slot ? 'MYO Slot Transferred' : 'Character Transferred', $data, 'user');
    }
}