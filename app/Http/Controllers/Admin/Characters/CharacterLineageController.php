<?php

namespace App\Http\Controllers\Admin\Characters;

use Illuminate\Http\Request;

use Auth;

use App\Models\Character\Character;
use App\Models\Character\CharacterImage;
use App\Models\Character\CharacterCategory;
use App\Models\Character\CharacterLineageBlacklist;
use App\Models\Rarity;
use App\Models\User\User;
use App\Models\Species\Species;
use App\Models\Species\Subtype;
use App\Models\Feature\Feature;

use App\Services\CharacterManager;

use App\Http\Controllers\Controller;

class CharacterLineageController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin / Character Lineage Controller
    |--------------------------------------------------------------------------
    |
    | Handles admin creation/editing of character lineage.
    |
    */

    /**
     * Shows the character lineage page.
     *
     * @param  string  $slug
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterLineagePage($slug)
    {
        return $this->getLineagePage($slug, false);
    }

    /**
     * Shows the MYO slot lineage page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getMyoLineagePage($id)
    {
        return $this->getLineagePage($id, true);
    }

    /**
     * Shows the character's lineage page.
     *
     * @param  string  $slug
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getLineagePage($id, $isMyo = false)
    {
        $this->character = $isMyo ? Character::where('is_myo_slot', 1)->where('id', $id)->first() : Character::where('slug', $id)->first();
        if(!$this->character) abort(404);

        $hasLineage = $this->character->lineage !== null;
        $line = $this->character->lineage;
        return view('character.lineage', [
            'character' => $this->character,
            'hasLineage' => $hasLineage,
            'lineage' => $line,
            'isMyo' => $isMyo,
        ]);
    }

    /**
     * Shows the edit character lineage modal.
     *
     * @param  string  $slug
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditCharacterLineage($slug)
    {
        return $this->getEditLineage($slug, false);
    }

    /**
     * Shows the edit MYO slot lineage modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditMyoLineage($id)
    {
        return $this->getEditLineage($id, true);
    }

    /**
     * Shows the edit lineage modal.
     *
     * @param  string/int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditLineage($id, $isMyo)
    {
        $this->character = $isMyo ? Character::where('is_myo_slot', 1)->where('id', $id)->first() : Character::where('slug', $id)->first();
        if(!$this->character) abort(404);

        $hasLineage = $this->character->lineage !== null;
        $line = $this->character->lineage;
        return view('character.admin._edit_lineage_modal', [
            'character' => $this->character,
            'characterOptions' => CharacterLineageBlacklist::getAncestorOptions(),
            'isMyo' => $isMyo,
            'hasLineage' => $hasLineage,
            'lineage' => [
                // there have GOT to be better ways to do this
                'sire_id'               => $hasLineage ? $line->sire_id : null,
                'sire_name'             => $hasLineage ? $line->sire_name : null,
                'sire_sire_id'          => $hasLineage ? $line->sire_sire_id : null,
                'sire_sire_name'        => $hasLineage ? $line->sire_sire_name : null,
                'sire_sire_sire_id'     => $hasLineage ? $line->sire_sire_sire_id : null,
                'sire_sire_sire_name'   => $hasLineage ? $line->sire_sire_sire_name : null,
                'sire_sire_dam_id'      => $hasLineage ? $line->sire_sire_dam_id : null,
                'sire_sire_dam_name'    => $hasLineage ? $line->sire_sire_dam_name : null,
                'sire_dam_id'           => $hasLineage ? $line->sire_dam_id : null,
                'sire_dam_name'         => $hasLineage ? $line->sire_dam_name : null,
                'sire_dam_sire_id'      => $hasLineage ? $line->sire_dam_sire_id : null,
                'sire_dam_sire_name'    => $hasLineage ? $line->sire_dam_sire_name : null,
                'sire_dam_dam_id'       => $hasLineage ? $line->sire_dam_dam_id : null,
                'sire_dam_dam_name'     => $hasLineage ? $line->sire_dam_dam_name : null,
                'dam_id'                => $hasLineage ? $line->dam_id : null,
                'dam_name'              => $hasLineage ? $line->dam_name : null,
                'dam_sire_id'           => $hasLineage ? $line->dam_sire_id : null,
                'dam_sire_name'         => $hasLineage ? $line->dam_sire_name : null,
                'dam_sire_sire_id'      => $hasLineage ? $line->dam_sire_sire_id : null,
                'dam_sire_sire_name'    => $hasLineage ? $line->dam_sire_sire_name : null,
                'dam_sire_dam_id'       => $hasLineage ? $line->dam_sire_dam_id : null,
                'dam_sire_dam_name'     => $hasLineage ? $line->dam_sire_dam_name : null,
                'dam_dam_id'            => $hasLineage ? $line->dam_dam_id : null,
                'dam_dam_name'          => $hasLineage ? $line->dam_dam_name : null,
                'dam_dam_sire_id'       => $hasLineage ? $line->dam_dam_sire_id : null,
                'dam_dam_sire_name'     => $hasLineage ? $line->dam_dam_sire_name : null,
                'dam_dam_dam_id'        => $hasLineage ? $line->dam_dam_dam_id : null,
                'dam_dam_dam_name'      => $hasLineage ? $line->dam_dam_dam_name : null,
            ],
        ]);
    }

    /**
     * Edits a character's lineage.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\CharacterManager  $service
     * @param  string                         $slug
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEditCharacterLineage(Request $request, CharacterManager $service, $slug)
    {
        return $this->postEditLineage($request, $service, $slug, false);
    }

    /**
     * Edits an MYO slot's lineage.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\CharacterManager  $service
     * @param  int                            $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEditMyoLineage(Request $request, CharacterManager $service, $id)
    {
        return $this->postEditLineage($request, $service, $id, true);
    }


    /**
     * Edits an char or myo's lineage.
     *
     * @param  \Illuminate\Http\Request       $request
     * @param  App\Services\CharacterManager  $service
     * @param  int                            $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEditLineage(Request $request, CharacterManager $service, $id, $isMyo)
    {
        $this->character = $isMyo ? Character::where('is_myo_slot', 1)->where('id', $id)->first() : Character::where('slug', $id)->first();
        if(!$this->character) abort(404);

        $data = $request->only([
            'sire_id',
            'sire_name',
            'sire_sire_id',
            'sire_sire_name',
            'sire_sire_sire_id',
            'sire_sire_sire_name',
            'sire_sire_dam_id',
            'sire_sire_dam_name',
            'sire_dam_id',
            'sire_dam_name',
            'sire_dam_sire_id',
            'sire_dam_sire_name',
            'sire_dam_dam_id',
            'sire_dam_dam_name',
            'dam_id',
            'dam_name',
            'dam_sire_id',
            'dam_sire_name',
            'dam_sire_sire_id',
            'dam_sire_sire_name',
            'dam_sire_dam_id',
            'dam_sire_dam_name',
            'dam_dam_id',
            'dam_dam_name',
            'dam_dam_sire_id',
            'dam_dam_sire_name',
            'dam_dam_dam_id',
            'dam_dam_dam_name',
            'generate_ancestors',
            'update_descendants',
        ]);
        if ($service->updateCharacterLineage($data, $this->character, Auth::user())) {
            flash('Character lineage updated successfully.')->success();
            return redirect()->to($this->character->url);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back()->withInput();
    }
}
