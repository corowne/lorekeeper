<?php

namespace App\Http\Controllers\Admin\Stats;

use Auth;
use Config;
use Settings;

use Illuminate\Http\Request;

use App\Models\Stats\User\Level;
use App\Models\Stats\Character\CharacterLevel;

use App\Services\Stats\LevelService;
use App\Services\CharacterManager;

use App\Http\Controllers\Controller;

class LevelController extends Controller
{
    // index for levels
    public function getIndex(Request $request)
    {
        $query = Level::query();
        $data = $request->only(['level']);
        if(isset($data['level'])) 
            $query->where('level', 'LIKE', '%'.$data['level'].'%');
        return view('admin.stats.user.user_levels', [
            'levels' => $query->paginate(20)->appends($request->query()),
        ]);
    }
    
    /**
     * Shows the create level page.
     */
    public function getCreateLevel()
    {
        return view('admin.stats.user.create_edit_level', [
            'level' => new Level,
        ]);
    }
    
    /**
     * Shows the edit level page.
     */
    public function getEditLevel($id)
    {
        $level = Level::find($id);
        if(!$level) abort(404);
        return view('admin.stats.user.create_edit_level', [
            'level' => $level,
        ]);
    }

     /**
     * Creates or edits an item.
     */
    public function postCreateEditLevel(Request $request, LevelService $service, $id = null)
    {
        $id ? $request->validate(Level::$updateRules) : $request->validate(Level::$createRules);
        $data = $request->only([
            'level', 'exp_required', 'stat_points'
        ]);
        if($id && $service->updateLevel(Level::find($id), $data)) {
            flash('Level updated successfully.')->success();
        }
        else if (!$id && $level = $service->createLevel($data, Auth::user())) {
            flash('Level created successfully.')->success();
            return redirect()->to('admin/levels/edit/'.$level->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Gets the level deletion modal.
     *
     */
    public function getDeleteLevel($id)
    {
        $level = Level::find($id);
        return view('admin.stats.user._delete_level', [
            'level' => $level,
        ]);
    }

    /**
     * Creates or edits an level.
     *
     */
    public function postDeleteLevel(Request $request, LevelService $service, $id)
    {
        if($id && $service->deleteLevel(Level::find($id))) {
            flash('Level deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/levels');
    }

    /**********************************************************************************************
    
        CHARACTERS

    **********************************************************************************************/

    // index for levels
    public function getCharaIndex(Request $request)
    {
        $query = CharacterLevel::query();
        $data = $request->only(['level']);
        if(isset($data['level'])) 
            $query->where('level', 'LIKE', '%'.$data['level'].'%');
        return view('admin.stats.character.character_levels', [
            'levels' => $query->paginate(20)->appends($request->query()),
        ]);
    }
    
    /**
     * Shows the create level page.
     */
    public function getCharaCreateLevel()
    {
        return view('admin.stats.character.create_edit_character_level', [
            'level' => new CharacterLevel,
        ]);
    }
    
    /**
     * Shows the edit level page.
     */
    public function getCharaEditLevel($id)
    {
        $level = CharacterLevel::find($id);
        if(!$level) abort(404);
        return view('admin.stats.character.create_edit_character_level', [
            'level' => $level,
        ]);
    }

     /**
     * Creates or edits an item.
     */
    public function postCharaCreateEditLevel(Request $request, LevelService $service, $id = null)
    {
        $id ? $request->validate(CharacterLevel::$updateRules) : $request->validate(CharacterLevel::$createRules);
        $data = $request->only([
            'level', 'exp_required', 'stat_points'
        ]);
        if($id && $service->updateCharaLevel(CharacterLevel::find($id), $data)) {
            flash('Level updated successfully.')->success();
        }
        else if (!$id && $level = $service->createCharaLevel($data, Auth::user())) {
            flash('Level created successfully.')->success();
            return redirect()->to('admin/levels/character/edit/'.$level->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

        /**
     * Gets the level deletion modal.
     *
     */
    public function getDeleteCharaLevel($id)
    {
        $level = CharacterLevel::find($id);
        return view('admin.stats.character._delete_level', [
            'level' => $level,
        ]);
    }

    /**
     * Creates or edits an level.
     *
     */
    public function postDeleteCharaLevel(Request $request, LevelService $service, $id)
    {
        if($id && $service->deleteCharaLevel(CharacterLevel::find($id))) {
            flash('Level deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/levels/character');
    }

}