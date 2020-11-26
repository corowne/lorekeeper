<?php

namespace App\Http\Controllers\Characters;

use Illuminate\Http\Request;

use Auth;

use App\Models\Character\Character;
use App\Models\Character\CharacterImage;
use App\Models\Character\CharacterCategory;
use App\Models\Character\CharacterLineage;
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
    | Character Lineage Controller
    |--------------------------------------------------------------------------
    |
    | Handles display of character lineage pages.
    |
    */

    /**
     * Shows the character lineage page.
     *
     * @param  string  $slug
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterLineage($slug)
    {
        return $this->getLineagePage($slug, false);
    }

    /**
     * Shows the MYO slot lineage page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getMyoLineage($id)
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
        // if they haven't got the option to have descendents...
        if($this->character->getLineageBlacklistLevel() > 0) abort(404);

        $hasLineage = $this->character->lineage !== null;
        $line = $this->character->lineage;
        return view('character.lineage', [
            'character' => $this->character,
            'hasLineage' => $hasLineage,
            'lineage' => $line,
            // TODO separate function for ancestors with no lineages
            'children' => $isMyo ? null : CharacterLineage::getChildrenStatic($this->character->id),
            'grandchildren' => $isMyo ? null : CharacterLineage::getGrandchildrenStatic($this->character->id),
            'greatGrandchildren' => $isMyo ? null : CharacterLineage::getGreatGrandchildrenStatic($this->character->id),
            'isMyo' => $isMyo,
        ]);
    }

    /**
     * Shows the character lineage page.
     *
     * @param  string  $slug
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterChildren($slug)
    {
        return $this->getChildren($slug, false);
    }

    /**
     * Shows the character lineage page.
     *
     * @param  string  $slug
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterGrandchildren($slug)
    {
        return $this->getGrandchildren($slug, false);
    }

    /**
     * Shows the character lineage page.
     *
     * @param  string  $slug
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterGreatGrandchildren($slug)
    {
        return $this->getGreatGrandchildren($slug, false);
    }

    /**
     * Shows the page for character children.
     *
     * @param  string  $slug
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getChildren($id, $isMyo = false)
    {
        $this->character = $isMyo ? Character::where('is_myo_slot', 1)->where('id', $id)->first() : Character::where('slug', $id)->first();
        if(!$this->character) abort(404);
        if($this->character->getLineageBlacklistLevel() > 0) abort(404);

        $children = $isMyo ? null : CharacterLineage::getChildrenStatic($this->character->id, false);
        return $this->getDescendantDisplay($this->character, "Children", $children, $isMyo);
    }

    /**
     * Shows the page for character grandchildren.
     *
     * @param  string  $slug
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getGrandchildren($id, $isMyo = false)
    {
        $this->character = $isMyo ? Character::where('is_myo_slot', 1)->where('id', $id)->first() : Character::where('slug', $id)->first();
        if(!$this->character) abort(404);
        if($this->character->getLineageBlacklistLevel() > 0) abort(404);

        $children = $isMyo ? null : CharacterLineage::getGrandchildrenStatic($this->character->id, false);
        return $this->getDescendantDisplay($this->character, "Grandchildren", $children, $isMyo);
    }

    /**
     * Shows the page for character children.
     *
     * @param  string  $slug
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getGreatGrandchildren($id, $isMyo = false)
    {
        $this->character = $isMyo ? Character::where('is_myo_slot', 1)->where('id', $id)->first() : Character::where('slug', $id)->first();
        if(!$this->character) abort(404);
        if($this->character->getLineageBlacklistLevel() > 0) abort(404);

        $children = $isMyo ? null : CharacterLineage::getGreatGrandchildrenStatic($this->character->id, false);
        return $this->getDescendantDisplay($this->character, "Great-Grandchildren", $children, $isMyo);
    }

    private function getDescendantDisplay($character, $title, $descendants, $isMyo = false)
    {
        return view('character.lineage_children', [
            'character' => $character,
            'title' => $title,
            'children' => $descendants,
            'isMyo' => $isMyo,
        ]);
    }
}
