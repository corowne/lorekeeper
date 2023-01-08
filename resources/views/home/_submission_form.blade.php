
@if($submission->status == 'Draft')
    {!! Form::open(['url' => $isClaim ? 'claims/edit' : 'submissions/edit', 'id' => 'submissionForm']) !!}
@else
    {!! Form::open(['url' => $isClaim ? 'claims/new' : 'submissions/new', 'id' => 'submissionForm']) !!}
@endif

    @if(Auth::check() && $submission->staff_comments && ($submission->user_id == Auth::user()->id || Auth::user()->hasPower('manage_submissions')))
        <h2>Staff Comments ({!! $submission->staff->displayName !!})</h2>
        <div class="card mb-3"><div class="card-body">
            @if(isset($submission->parsed_staff_comments))
                {!! $submission->parsed_staff_comments !!}
            @else
                {!! $submission->staff_comments !!}
            @endif
        </div></div>
    @endif

    @if(!$isClaim)
        <div class="form-group">
            {!! Form::label('prompt_id', 'Prompt') !!}
            {!! Form::select('prompt_id', $prompts, (isset($submission->prompt_id) ? $submission->prompt_id : Request::get('prompt_id')), ['class' => 'form-control selectize', 'id' => 'prompt', 'placeholder' => '']) !!}
        </div>
    @endif

    <div class="form-group">
        {!! Form::label('url', $isClaim ? 'URL (Optional)' : 'Submission URL (Optional)') !!}
        @if($isClaim)
            {!! add_help('Enter a URL relevant to your claim (for example, a comment proving you may make this claim).') !!}
        @else
            {!! add_help('Enter the URL of your submission (whether uploaded to dA or some other hosting service).') !!}
        @endif
        {!! Form::text('url', (isset($submission->url) ? $submission->url : Request::get('url')), ['class' => 'form-control', 'required']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('comments', 'Comments (Optional)') !!} {!! add_help('Enter a comment for your ' . ($isClaim ? 'claim' : 'submission') . ' (no HTML). This will be viewed by the mods when reviewing your ' . ($isClaim ? 'claim' : 'submission') . '.') !!}
        {!! Form::textarea('comments', (isset($submission->comments) ? $submission->comments : Request::get('comments')), ['class' => 'form-control']) !!}
    </div>

    @if($submission->prompt_id)
        <div class="mb-3">
            @include('home._prompt', ['prompt' => $submission->prompt, 'staffView' => false])
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-header h2">
            Rewards
        </div>
        <div class="card-body">
            @if($isClaim)
                <p>Select the rewards you would like to claim.</p>
            @else
                <p>Note that any rewards added here are <u>in addition</u> to the default prompt rewards. If you do not require any additional rewards, you can leave this blank.</p>
            @endif
            @if($isClaim)
                @include('widgets._loot_select', ['loots' => $submission->rewards, 'showLootTables' => false, 'showRaffles' => true])
            @else
                @include('widgets._loot_select', ['loots' => $submission->rewards, 'showLootTables' => false, 'showRaffles' => false])
            @endif

            @if(!$isClaim)
                <div id="rewards" class="mb-3"></div>
            @endif
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header h2">
            <a href="#" class="btn btn-outline-info float-right" id="addCharacter">Add Character</a>
            Characters
        </div>
        <div class="card-body" style="clear:both;">
            @if($isClaim)
                <p>If there are character-specific rewards you would like to claim, attach them here. Otherwise, this section can be left blank.</p>
            @endif
            <div id="characters" class="mb-3">
                @foreach($submission->characters as $character)
                    @include('widgets._character_select_entry', ['characterCurrencies' => $characterCurrencies, 'items' => $items, 'tables' => [], 'showTables' => false, 'character' => $character, 'expanded_rewards' => $expanded_rewards])
                @endforeach
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header h2">
            Add-Ons
        </div>
        <div class="card-body">
            <p>If your {{ $isClaim ? 'claim' : 'submission' }} consumes items, attach them here. Otherwise, this section can be left blank. These items will be removed from your inventory but refunded if your {{ $isClaim ? 'claim' : 'submission' }} is rejected.</p>
            <div id="addons" class="mb-3">
            @include('widgets._inventory_select', ['user' => Auth::user(), 'inventory' => $inventory, 'categories' => $categories, 'selected' => $submission->getInventory($submission->user), 'page' => $page])
            @include('widgets._bank_select', ['owner' => Auth::user(), 'selected' => $submission->getCurrencies($submission->user)])
            </div>
        </div>
    </div>

    @if($submission->status == 'Draft')
        <div class="text-right">
            <a href="#" class="btn btn-danger mr-2" id="cancelButton">Cancel Draft</a>
            <a href="#" class="btn btn-secondary mr-2" id="draftButton">Save Draft</a>
            <a href="#" class="btn btn-primary" id="confirmButton">Submit</a>
        </div>
    @else
        <div class="text-right">
            <a href="#" class="btn btn-secondary mr-2" id="draftButton">Save Draft</a>
            <a href="#" class="btn btn-primary" id="confirmButton">Submit</a>
        </div>
    @endif

{!! Form::close() !!}

@include('widgets._character_select', ['characterCurrencies' => $characterCurrencies, 'showLootTables' => false])
@if($isClaim)
    @include('widgets._loot_select_row', ['items' => $items, 'currencies' => $currencies, 'showLootTables' => false, 'showRaffles' => true])
@else
    @include('widgets._loot_select_row', ['items' => $items, 'currencies' => $currencies, 'showLootTables' => false, 'showRaffles' => false])
@endif
