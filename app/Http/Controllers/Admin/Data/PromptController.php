<?php

namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;

use Auth;

use App\Models\Prompt\PromptCategory;
use App\Models\Prompt\Prompt;
use App\Models\Item\Item;
use App\Models\Currency\Currency;
use App\Models\Loot\LootTable;
use App\Models\Raffle\Raffle;

use App\Services\PromptService;

use App\Http\Controllers\Controller;

class PromptController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin / Prompt Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of prompt categories and prompts.
    |
    */

    /**
     * Shows the prompt category index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('admin.prompts.prompt_categories', [
            'categories' => PromptCategory::orderBy('sort', 'DESC')->get()
        ]);
    }

    /**
     * Shows the create prompt category page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreatePromptCategory()
    {
        return view('admin.prompts.create_edit_prompt_category', [
            'category' => new PromptCategory
        ]);
    }

    /**
     * Shows the edit prompt category page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditPromptCategory($id)
    {
        $category = PromptCategory::find($id);
        if(!$category) abort(404);
        return view('admin.prompts.create_edit_prompt_category', [
            'category' => $category
        ]);
    }

    /**
     * Creates or edits a prompt category.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  App\Services\PromptService  $service
     * @param  int|null                    $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditPromptCategory(Request $request, PromptService $service, $id = null)
    {
        $id ? $request->validate(PromptCategory::$updateRules) : $request->validate(PromptCategory::$createRules);
        $data = $request->only([
            'name', 'description', 'image', 'remove_image'
        ]);
        if($id && $service->updatePromptCategory(PromptCategory::find($id), $data, Auth::user())) {
            flash('Category updated successfully.')->success();
        }
        else if (!$id && $category = $service->createPromptCategory($data, Auth::user())) {
            flash('Category created successfully.')->success();
            return redirect()->to('admin/data/prompt-categories/edit/'.$category->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Gets the prompt category deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeletePromptCategory($id)
    {
        $category = PromptCategory::find($id);
        return view('admin.prompts._delete_prompt_category', [
            'category' => $category,
        ]);
    }

    /**
     * Deletes a prompt category.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  App\Services\PromptService  $service
     * @param  int                         $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeletePromptCategory(Request $request, PromptService $service, $id)
    {
        if($id && $service->deletePromptCategory(PromptCategory::find($id))) {
            flash('Category deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/prompt-categories');
    }

    /**
     * Sorts prompt categories.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  App\Services\PromptService  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSortPromptCategory(Request $request, PromptService $service)
    {
        if($service->sortPromptCategory($request->get('sort'))) {
            flash('Category order updated successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }


    /**********************************************************************************************

        PROMPTS

    **********************************************************************************************/

    /**
     * Shows the prompt category index.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getPromptIndex(Request $request)
    {
        $query = Prompt::query();
        $data = $request->only(['prompt_category_id', 'name']);
        if(isset($data['prompt_category_id']) && $data['prompt_category_id'] != 'none')
            $query->where('prompt_category_id', $data['prompt_category_id']);
        if(isset($data['name']))
            $query->where('name', 'LIKE', '%'.$data['name'].'%');
        return view('admin.prompts.prompts', [
            'prompts' => $query->paginate(20)->appends($request->query()),
            'categories' => ['none' => 'Any Category'] + PromptCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray()
        ]);
    }

    /**
     * Shows the create prompt page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreatePrompt()
    {
        return view('admin.prompts.create_edit_prompt', [
            'prompt' => new Prompt,
            'categories' => ['none' => 'No category'] + PromptCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Shows the edit prompt page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditPrompt($id)
    {
        $prompt = Prompt::find($id);
        if(!$prompt) abort(404);
        return view('admin.prompts.create_edit_prompt', [
            'prompt' => $prompt,
            'categories' => ['none' => 'No category'] + PromptCategory::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Creates or edits a prompt.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  App\Services\PromptService  $service
     * @param  int|null                    $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditPrompt(Request $request, PromptService $service, $id = null)
    {
        $id ? $request->validate(Prompt::$updateRules) : $request->validate(Prompt::$createRules);
        $data = $request->only([
            'name', 'prompt_category_id', 'summary', 'description', 'start_at', 'end_at', 'hide_before_start', 'hide_after_end', 'is_active', 'rewardable_type', 'rewardable_id', 'quantity', 'image', 'remove_image', 'prefix', 'hide_submissions'
        ]);
        if($id && $service->updatePrompt(Prompt::find($id), $data, Auth::user())) {
            flash('Prompt updated successfully.')->success();
        }
        else if (!$id && $prompt = $service->createPrompt($data, Auth::user())) {
            flash('Prompt created successfully.')->success();
            return redirect()->to('admin/data/prompts/edit/'.$prompt->id);
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->back();
    }

    /**
     * Gets the prompt deletion modal.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeletePrompt($id)
    {
        $prompt = Prompt::find($id);
        return view('admin.prompts._delete_prompt', [
            'prompt' => $prompt,
        ]);
    }

    /**
     * Deletes a prompt.
     *
     * @param  \Illuminate\Http\Request    $request
     * @param  App\Services\PromptService  $service
     * @param  int                         $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeletePrompt(Request $request, PromptService $service, $id)
    {
        if($id && $service->deletePrompt(Prompt::find($id))) {
            flash('Prompt deleted successfully.')->success();
        }
        else {
            foreach($service->errors()->getMessages()['error'] as $error) flash($error)->error();
        }
        return redirect()->to('admin/data/prompts');
    }
}
