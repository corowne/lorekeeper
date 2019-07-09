<?php

namespace App\Http\Controllers\Admin\Characters;

use Illuminate\Http\Request;

use Auth;

use App\Models\Character\Character;
use App\Models\Character\CharacterImage;
use App\Models\Character\CharacterCategory;
use App\Models\Rarity;
use App\Models\User\User;
use App\Models\Species;
use App\Models\Feature\Feature;

use App\Services\CharacterManager;

use App\Http\Controllers\Controller;

class CharacterImageController extends Controller
{
    /**
     * Show the add image page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getNewImage($slug)
    {
        $this->character = Character::where('slug', $slug)->first();
        if(!$this->character) abort(404);

        return view('character.admin.upload_image', [
            'character' => $this->character,
            'rarities' => ['0' => 'Select Rarity'] + Rarity::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'specieses' => ['0' => 'Select Species'] + Species::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'features' => Feature::orderBy('name')->pluck('name', 'id')->toArray()
        ]);
    }

    public function postNewImage(Request $request, CharacterManager $service, $slug)
    {
        $request->validate(CharacterImage::$createRules);
        $data = $request->only(['image', 'thumbnail', 'x0', 'x1', 'y0', 'y1', 'use_cropper', 'artist_url', 'artist_alias', 'designer_url', 'designer_alias', 'species_id', 'rarity_id', 'feature_id', 'feature_data', 'is_valid', 'is_visible']);
        $this->character = Character::where('slug', $slug)->first();
        if(!$this->character) abort(404);
        if($service->createImage($data, $this->character, Auth::user())) {
            flash('Image uploaded successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
            return redirect()->back();
        }
        return redirect()->to($this->character->url.'/images');
    }

    /**
     * Show the edit image features modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditImageFeatures($id)
    {
        return view('character.admin._edit_features_modal', [
            'image' => CharacterImage::find($id),
            'rarities' => ['0' => 'Select Rarity'] + Rarity::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'specieses' => ['0' => 'Select Species'] + Species::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'features' => Feature::orderBy('name')->pluck('name', 'id')->toArray()
        ]);
    }

    public function postEditImageFeatures(Request $request, CharacterManager $service, $id)
    {
        $data = $request->only(['species_id', 'rarity_id', 'feature_id', 'feature_data']);
        $image = CharacterImage::find($id);
        if(!$image) abort(404);
        if($service->updateImageFeatures($data, $image, Auth::user())) {
            flash('Character traits edited successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Show the edit image notes modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditImageNotes($id)
    {
        return view('character.admin._edit_notes_modal', [
            'image' => CharacterImage::find($id),
        ]);
    }

    public function postEditImageNotes(Request $request, CharacterManager $service, $id)
    {
        $data = $request->only(['description']);
        $image = CharacterImage::find($id);
        if(!$image) abort(404);
        if($service->updateImageNotes($data, $image, Auth::user())) {
            flash('Image notes edited successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Show the edit image credits modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditImageCredits($id)
    {
        return view('character.admin._edit_credits_modal', [
            'image' => CharacterImage::find($id),
        ]);
    }

    public function postEditImageCredits(Request $request, CharacterManager $service, $id)
    {
        $data = $request->only(['artist_url', 'artist_alias', 'designer_url', 'designer_alias']);
        $image = CharacterImage::find($id);
        if(!$image) abort(404);
        if($service->updateImageCredits($data, $image, Auth::user())) {
            flash('Image credits edited successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Show the reupload image modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getImageReupload($id)
    {
        return view('character.admin._reupload_image_modal', [
            'image' => CharacterImage::find($id),
        ]);
    }

    public function postImageReupload(Request $request, CharacterManager $service, $id)
    {
        $data = $request->only(['image', 'thumbnail', 'x0', 'x1', 'y0', 'y1', 'use_cropper']);
        $image = CharacterImage::find($id);
        if(!$image) abort(404);
        if($service->reuploadImage($data, $image, Auth::user())) {
            flash('Image uploaded successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    public function postImageSettings(Request $request, CharacterManager $service, $id)
    {
        $data = $request->only(['is_valid', 'is_visible']);
        $image = CharacterImage::find($id);
        if(!$image) abort(404);
        if($service->updateImageSettings($data, $image, Auth::user())) {
            flash('Image settings edited successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Show the set active image modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getImageActive($id)
    {
        return view('character.admin._active_image_modal', [
            'image' => CharacterImage::find($id),
        ]);
    }
    
    public function postImageActive(Request $request, CharacterManager $service, $id)
    {
        $image = CharacterImage::find($id);
        if(!$image) abort(404);
        if($service->updateActiveImage($image, Auth::user())) {
            flash('Active character image set successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Show the delete image modal.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getImageDelete($id)
    {
        return view('character.admin._delete_image_modal', [
            'image' => CharacterImage::find($id),
        ]);
    }
    
    public function postImageDelete(Request $request, CharacterManager $service, $id)
    {
        $image = CharacterImage::find($id);
        if(!$image) abort(404);
        if($service->deleteImage($image, Auth::user())) {
            flash('Character image deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    public function postSortImages(Request $request, CharacterManager $service, $slug)
    {
        $this->character = Character::where('slug', $slug)->first();
        if(!$this->character) abort(404);

        if ($service->sortImages($request->only(['sort']), $this->character, Auth::user())) {
            flash('Images sorted successfully.')->success();
            return redirect()->back();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }
}
