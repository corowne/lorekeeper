@extends('galleries.layout')

@section('gallery-title') {{ $submission->id ? 'Edit' : 'Create' }}Submission @endsection

@section('gallery-content')
{!! breadcrumbs(['Gallery' => 'gallery', $gallery->name => 'gallery/'.$gallery->id, ($submission->id ? 'Edit' : 'Create').' Submission' => $submission->id ? 'gallery/submissions/edit/'.$submission->id : gallery/submit/'.$gallery->id]) !!}

<h1>{{ $submission->id ? 'Edit' : 'Create' }} Gallery Submission
    @if($submission->id)
        <a href="#" class="btn btn-outline-danger float-right delete-submission-button">Delete Submission</a>
    @endif
</h1>

@if(!$submission->id && ($closed || !$gallery->canSubmit))
    <div class="alert alert-danger">
        @if($closed) Gallery submissions are currently closed.
        @else You can't submit to this gallery. @endif
    </div>
@else 
    {!! Form::open(['url' => $submission->id ? 'gallery/submission/edit/'.$submission->id : 'gallery/submit'.$gallery->id, 'files' => true]) !!}

        <h2>Main Content</h2>
        <p>Upload an image and/or text as the content of your submission. You <strong>can</strong> upload both in the event that you have an image with accompnaying text or vice versa.</p>

        <div class="form-group">
            {!! Form::label('Image') !!}
            <div>{!! Form::file('image') !!}</div>
        </div>

        <div class="form-group">
            {!! Form::label('Text') !!}
            {!! Form::textarea('text', $submission->text, ['class' => 'form-control wysiwyg']) !!}
        </div>

        <h3>Basic Information</h3>
        <div class="form-group">
            {!! Form::label('Title') !!}
            {!! Form::text('title', $submission->title, ['class' => 'form-control']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('Description (Optional)') !!}
            {!! Form::textarea('description', $item->description, ['class' => 'form-control wysiwyg']) !!}
        </div>


        <div class="form-group">
            {!! Form::label('prompt_id', 'Prompt') !!}
            {!! Form::select('prompt_id', $prompts, Request::get('prompt_id'), ['class' => 'form-control selectize', 'id' => 'prompt', 'placeholder' => '']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('url', $isClaim ? 'URL' : 'Submission URL') !!} 
            @if($isClaim) 
                {!! add_help('Enter a URL relevant to your claim (for example, a comment proving you may make this claim). This field cannot be left blank.') !!} 
            @else 
                {!! add_help('Enter the URL of your submission (whether uploaded to dA or some other hosting service). This field cannot be left blank.') !!} 
            @endif
            {!! Form::text('url', null, ['class' => 'form-control', 'required']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('comments', 'Comments (Optional)') !!} {!! add_help('Enter a comment for your ' . ($isClaim ? 'claim' : 'submission') . ' (no HTML). This will be viewed by the mods when reviewing your ' . ($isClaim ? 'claim' : 'submission') . '.') !!}
            {!! Form::textarea('comments', null, ['class' => 'form-control']) !!}
        </div>

        <h2>Characters</h2>
        <p>Attach characters associated with this piece.</p>
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
                    <span class="modal-title h5 mb-0">Confirm  Submission</span>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>This will submit the form and put it into the approval queue. You will not be able to edit the contents after the  {{ $isClaim ? 'claim' : 'submission' }} has been made. Click the Confirm button to complete the  {{ $isClaim ? 'claim' : 'submission' }}.</p>
                    <div class="text-right">
                        <a href="#" id="formSubmit" class="btn btn-primary">Confirm</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@section('scripts')
@parent 
@if(!$closed)
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
@endif
@endsection