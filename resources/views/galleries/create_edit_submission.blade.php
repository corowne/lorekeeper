@extends('galleries.layout')

@section('gallery-title') {{ $submission->id ? 'Edit' : 'Create' }} Submission @endsection

@section('gallery-content')
{!! breadcrumbs(['Gallery' => 'gallery', $gallery->name => 'gallery/'.$gallery->id, ($submission->id ? 'Edit' : 'Create').' Submission' => $submission->id ? 'gallery/submissions/edit/'.$submission->id : 'gallery/submit/'.$gallery->id]) !!}

<h1>{{ $submission->id ? 'Edit Submission' : 'Submit to '.$gallery->name }}
    @if($submission->id)
        <a href="#" class="btn btn-outline-danger float-right delete-submission-button">Archive Submission</a>
    @endif
</h1>

@if(!$submission->id && ($closed || !$gallery->canSubmit))
    <div class="alert alert-danger">
        @if($closed) Gallery submissions are currently closed.
        @else You can't submit to this gallery. @endif
    </div>
@else 
    {!! Form::open(['url' => $submission->id ? 'gallery/edit/'.$submission->id : 'gallery/submit', 'id' => 'gallerySubmissionForm', 'files' => true]) !!}

        <h2>Main Content</h2>
        <p>Upload an image and/or text as the content of your submission. You <strong>can</strong> upload both in the event that you have an image with accompanying text or vice versa.</p>

        <div class="form-group">
            {!! Form::label('Image') !!}
            @if($submission->id && isset($submission->hash) && $submission->hash)
                <div class="card mb-2" id="existingImage">
                    <div class="card-body text-center">
                        <img src="{{ $submission->imageUrl }}" style="max-width:100%; max-height:60vh;" />
                    </div>
                </div>
            @endif
            <div class="card mb-2 hide" id="imageContainer">
                <div class="card-body text-center">
                    <img src="#" id="image" style="max-width:100%; max-height:60vh;" />
                </div>
            </div>
            <div class="card p-2">
                {!! Form::file('image', ['id' => 'mainImage']) !!}
                <small>Images may be PNG, GIF, or JPG and up to 4MB in size.</small>
            </div>
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
                                            <div class="d-flex">{!! $collaborator->has_approved ? '<div class="btn btn-success mb-2 mr-2"><i class="fas fa-check"></i></div>' : '' !!}{!! Form::select('collaborator_id[]', $users, $collaborator->user_id, ['class' => 'form-control mr-2 collaborator-select original', 'placeholder' => 'Select User']) !!}</div>
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
                @if(Settings::get('gallery_submissions_reward_currency') && $gallery->currency_enabled)
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
                                <h6>Form Responses:</h6>
                                @foreach($submission->data['currencyData'] as $key=>$data)
                                    <p>
                                        @if(isset($data))
                                            <strong>{{ Config::get('lorekeeper.group_currency_form')[$key]['name'] }}:</strong><br/>
                                            @if(Config::get('lorekeeper.group_currency_form')[$key]['type'] == 'choice')
                                                @if(isset(Config::get('lorekeeper.group_currency_form')[$key]['multiple']) && Config::get('lorekeeper.group_currency_form')[$key]['multiple'] == 'true')
                                                    @foreach($data as $answer)
                                                        {{ Config::get('lorekeeper.group_currency_form')[$key]['choices'][$answer] }}<br/>
                                                    @endforeach
                                                @else
                                                    {{ Config::get('lorekeeper.group_currency_form')[$key]['choices'][$data] }}
                                                @endif
                                            @else
                                                {{ Config::get('lorekeeper.group_currency_form')[$key]['type'] == 'checkbox' ? (Config::get('lorekeeper.group_currency_form')[$key]['value'] == $data ? 'True' : 'False') : $data }}
                                            @endif
                                        @endif
                                    </p>
                                @endforeach
                                @if(Auth::user()->hasPower('manage_submissions'))
                                <h6>[Admin]</h6>
                                    <p>
                                        <strong>Calculated Total:</strong> {{ $submission->data['total'] }}
                                        @if($submission->characters->count() > 1)
                                            <br/><strong>Total times Number of Characters:</strong> {{ round($submission->data['total'] * $submission->characters->count()) }}
                                        @endif
                                        @if($submission->collaborators->count())
                                            <br/><strong>Total divided by Number of Collaborators:</strong> {{ round(round($submission->data['total'] * $submission->characters->count()) / $submission->collaborators->count()) }}
                                        @endif
                                    </p>
                                @endif
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
                    <p>
                        @if(!$submission->id)
                            This will submit the form and put it into the approval queue. You will not be able to edit certain parts after the submission has been made, so please review the contents before submitting. Click the Confirm button to complete the submission.
                        @else
                            This will update the submission.{{ $submission->status == 'Pending' ? ' If you have added or removed any collaborators, they will not be informed (so as not to send additional notifications to previously notified collaborators), so please make sure to do so if necessary as all collaborators must approve a submission for it to be submitted for admin approval.' : '' }}
                        @endif
                    </p>
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

            var $image = $('#image');
            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $image.attr('src', e.target.result);
                        $('#existingImage').addClass('hide');
                        $('#imageContainer').removeClass('hide');
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
            $("#mainImage").change(function() {
                readURL(this);
            });
            
        });
    </script>
@endif
@endsection