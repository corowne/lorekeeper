@extends('galleries.layout')

@section('gallery-title') {{ $submission->id ? 'Edit' : 'Create' }} Submission @endsection

@section('gallery-content')
{!! breadcrumbs(['Gallery' => 'gallery', $gallery->name => 'gallery/'.$gallery->id, ($submission->id ? 'Edit' : 'Create').' Submission' => $submission->id ? 'gallery/submissions/edit/'.$submission->id : 'gallery/submit/'.$gallery->id]) !!}

<h1>{{ $submission->id ? 'Edit Submission' : 'Submit to '.$gallery->name }}
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
    {!! Form::open(['url' => $submission->id ? 'gallery/submission/edit/'.$submission->id : 'gallery/submit', 'id' => 'gallerySubmissionForm', 'files' => true]) !!}

        <h2>Main Content</h2>
        <p>Upload an image and/or text as the content of your submission. You <strong>can</strong> upload both in the event that you have an image with accompanying text or vice versa.</p>

        <div class="form-group">
            {!! Form::label('Image') !!}
            <div>{!! Form::file('image') !!}</div>
        </div>

        <div class="form-group">
            {!! Form::label('Text') !!}
            {!! Form::textarea('text', $submission->text, ['class' => 'form-control wysiwyg']) !!}
        </div>

        <div class="row">
            <div class="col-md">
                <h3>Basic Information</h3>
                <div class="form-group">
                    {!! Form::label('Title') !!}
                    {!! Form::text('title', $submission->title, ['class' => 'form-control']) !!}
                </div>

                <div class="form-group">
                    {!! Form::label('Description (Optional)') !!}
                    {!! Form::textarea('description', $submission->description, ['class' => 'form-control wysiwyg']) !!}
                </div>

                @if(!$submission->id)
                    <div class="form-group">
                        {!! Form::label('prompt_id', 'Prompt (Optional)') !!} {!! add_help('This <strong>does not</strong> automatically submit to the selected prompt, and you will need to submit to it separately. The prompt selected here will be displayed on the submission page for future reference. You will not be able to edit this after creating the submission.') !!}
                        {!! Form::select('prompt_id', $prompts, null, ['class' => 'form-control selectize', 'id' => 'prompt', 'placeholder' => 'Select a Prompt']) !!}
                    </div>
                @else
                    {!! $submission->prompt_id ? '<p><strong>Prompt:</strong> '.$submission->prompt->displayName.'</p>' : '' !!}
                @endif

                {!! Form::hidden('gallery_id', $gallery->id) !!}

                <h3>Characters</h3>
                <p>
                    Add the characters included in this piece.
                    @if(Settings::get('gallery_submissions_reward_currency'))
                     This helps the staff processing your submission award {!! $currency->displayName !!} for it, so be sure to add every character.
                    @endif
                </p>
                <div id="characters" class="mb-3">
                    @if($submission->id)
                        @foreach($submission->characters as $character)
                            @include('galleries._character_select_entry', ['character' => $character])
                        @endforeach
                    @endif
                </div>
                <div class="text-right mb-3">
                    <a href="#" class="btn btn-outline-info" id="addCharacter">Add Character</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Collaborator Info</h5>
                    </div>
                    <div class="card-body">
                        <p>If this piece is a collaboration, add collaborators and their roles here, including yourself. <strong>Otherwise, leave this blank</strong>. You <strong>will not</strong> be able to edit this once the submission has been accepted.</p>
                        @if(!$submission->id || $submission->status == 'Pending')
                            <div class="text-right mb-3">
                                <a href="#" class="btn btn-outline-info" id="add-collaborator">Add Collaborator</a>
                            </div>
                            <div id="collaboratorList">
                                @if($submission->id)
                                    @foreach($submission->collaborators as $collaborator)
                                        <div class="mb-2">
                                            <div class="d-flex">{!! $collaborator->has_approved ? '<div class="btn btn-success"><i class="fas fa-check"></i></div>' : '' !!}{!! Form::select('collaborator_id[]', $users, $collaborator->user_id, ['class' => 'form-control mr-2 collaborator-select original', 'placeholder' => 'Select User']) !!}</div>
                                            <div class="d-flex">
                                                {!! Form::text('collaborator_data[]', $collaborator->data, ['class' => 'form-control mr-2', 'placeholder' => 'Role (Sketch, Lines, etc.)']) !!}
                                                <a href="#" class="remove-collaborator btn btn-danger mb-2">×</a>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        @else
                            <p>
                                @if($submission->collaborators->count())
                                    @foreach($submission->collaborators as $collaborator)
                                        {!! $collaborator->user->displayName !!}: {{ $collaborator->data }}<br/>
                                    @endforeach
                                @endif
                            </p>
                        @endif
                    </div>
                </div>
                @if(Settings::get('gallery_submissions_reward_currency'))
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>{!! $currency->name !!} Awards</h5>
                        </div>
                        <div class="card-body">
                            @if(!$submission->id)
                                <p>Please select options as appropriate for this piece. This will help the staff processing your submission award {!! $currency->displayName !!} for it. You <strong>will not</strong> be able to edit this after creating the submission.</p>
                                {!! form_row($form->start) !!}
                                {!!  form_rest($form) !!}
                            @else
                                <p>a</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="text-right">
            <a href="#" class="btn btn-primary" id="submitButton">Submit</a>
        </div>
    {!! Form::close() !!}

    @include('galleries._character_select')
    <div class="collaborator-row hide mb-2">
        {!! Form::select('collaborator_id[]', $users, null, ['class' => 'form-control mr-2 collaborator-select', 'placeholder' => 'Select User']) !!}
        <div class="d-flex">
            {!! Form::text('collaborator_data[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'Role (Sketch, Lines, etc.)']) !!}
            <a href="#" class="remove-collaborator btn btn-danger mb-2">×</a>
        </div>
    </div>

    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="modal-title h5 mb-0">Confirm  Submission</span>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>This will submit the form and put it into the approval queue. You will not be able to edit certain parts after the submission has been made, so please review the contents before submitting. Click the Confirm button to complete the submission.</p>
                    <div class="text-right">
                        <a href="#" id="formSubmit" class="btn btn-primary">Confirm</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<?php $galleryPage = true; 
$sideGallery = $gallery ?>
@endsection

@section('scripts')
@parent 
@if(!$closed || ($submission->id && $submission->status != 'Rejected'))
    @include('galleries._character_select_js')

    <script>
        $(document).ready(function() {
            var $submitButton = $('#submitButton');
            var $confirmationModal = $('#confirmationModal');
            var $formSubmit = $('#formSubmit');
            var $gallerySubmissionForm = $('#gallerySubmissionForm');
            
            $submitButton.on('click', function(e) {
                e.preventDefault();
                $confirmationModal.modal('show');
            });

            $formSubmit.on('click', function(e) {
                e.preventDefault();
                $gallerySubmissionForm.submit();
            });

            $('.original.collaborator-select').selectize();
            $('#add-collaborator').on('click', function(e) {
                e.preventDefault();
                addCollaboratorRow();
            });
            $('.remove-collaborator').on('click', function(e) {
                e.preventDefault();
                removeCollaboratorRow($(this));
            })
            function addCollaboratorRow() {
                var $clone = $('.collaborator-row').clone();
                $('#collaboratorList').append($clone);
                $clone.removeClass('hide collaborator-row');
                $clone.find('.remove-collaborator').on('click', function(e) {
                    e.preventDefault();
                    removeCollaboratorRow($(this));
                })
                $clone.find('.collaborator-select').selectize();
            }
            function removeCollaboratorRow($trigger) {
                $trigger.parent().parent().remove();
            }
        });
    </script>
@endif
@endsection