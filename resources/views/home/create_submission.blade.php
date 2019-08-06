@extends('home.layout')

@section('home-title') New Submission @endsection

@section('home-content')
{!! breadcrumbs(['Prompt Submissions' => 'submissions', 'New Submission' => 'submissions/new']) !!}

<h1>
    New Submission
</h1>

{!! Form::open(['url' => 'submissions/new', 'id' => 'submissionForm']) !!}
    <div class="form-group">
        {!! Form::label('prompt_id', 'Prompt') !!}
        {!! Form::select('prompt_id', $prompts, null, ['class' => 'form-control selectize', 'id' => 'prompt', 'placeholder' => '']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('url', 'Submission URL') !!} {!! add_help('Enter the URL of your submission (whether uploaded to dA or some other hosting service). This field cannot be left blank.') !!}
        {!! Form::text('url', null, ['class' => 'form-control', 'required']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('comments', 'Comments (Optional)') !!} {!! add_help('Enter a comment for your submission (no HTML). This will be viewed by the mods when reviewing your submission.') !!}
        {!! Form::textarea('comments', null, ['class' => 'form-control']) !!}
    </div>

    <h2>Rewards</h2>
    <p>Note that any rewards added here are <u>in addition</u> to the default prompt rewards. If you do not require any additional rewards, you can leave this blank.</p>
    @include('widgets._loot_select', ['loots' => null, 'showLootTables' => false])
    <div id="rewards" class="mb-3">
        
    </div>

    <h2>Characters</h2>
    <div id="characters" class="mb-3">
    </div>
    <div class="text-right mb-3">
        <a href="#" class="btn btn-outline-info" id="addCharacter">Add Character</a>
    </div>

    <div class="text-right">
        <a href="#" class="btn btn-primary" id="submitButton">Submit</a>
    </div>
{!! Form::close() !!}

@include('widgets._character_select', ['characterCurrencies' => $characterCurrencies])
@include('widgets._loot_select_row', ['items' => $items, 'currencies' => $currencies, 'showLootTables' => false])


<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title h5 mb-0">Confirm Submission</span>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>This will submit the form and put it into the prompt approval queue. You will not be able to edit the contents after the submission has been made. Click the Confirm button to complete the submission.</p>
                <div class="text-right">
                    <a href="#" id="formSubmit" class="btn btn-primary">Confirm</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@parent 
@include('js._loot_js', ['showLootTables' => false])
@include('js._character_select_js')

<script>
    $(document).ready(function() {
        var $submitButton = $('#submitButton');
        var $confirmationModal = $('#confirmationModal');
        var $formSubmit = $('#formSubmit');
        var $submissionForm = $('#submissionForm');
        var $prompt = $('#prompt');
        var $rewards = $('#rewards');

        $prompt.selectize();
        $prompt.on('change', function(e) {
            $rewards.load('{{ url('submissions/new/prompt') }}/'+$(this).val());
        });
        
        $submitButton.on('click', function(e) {
            e.preventDefault();
            $confirmationModal.modal('show');
        });

        $formSubmit.on('click', function(e) {
            e.preventDefault();
            $submissionForm.submit();
        });
    });
</script>
@endsection