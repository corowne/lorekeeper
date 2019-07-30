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
    <div id="rewards" class="mb-3">
        Select a prompt.
    </div>

    <h2>Characters</h2>
    <div id="characters" class="mb-3">
    </div>
    <div class="text-right mb-3">
        <a href="#" class="btn btn-outline-primary" id="addCharacter">Add Character</a>
    </div>

    <div class="text-right">
        <a href="#" class="btn btn-primary" id="submitButton">Submit</a>
    </div>
{!! Form::close() !!}

<div id="components" class="hide">
    <div class="submission-character mb-3 card">
        <div class="card-body">
            <div class="text-right"><a href="#" class="remove-character text-muted"><i class="fas fa-times"></i></a></div>
            <div class="row">
                <div class="col-md-2 align-items-stretch d-flex">
                    <div class="d-flex text-center align-items-center">
                        <div class="character-image-blank">Enter character code.</div>
                        <div class="character-image-loaded hide"></div>
                    </div>
                </div>
                <div class="col-md-10">
                    <a href="#" class="float-right fas fa-close"></a>
                    <div class="form-group">
                        {!! Form::label('slug[]', 'Character Code') !!}
                        {!! Form::text('slug[]', null, ['class' => 'form-control character-code']) !!}
                    </div>
                    <div class="character-rewards hide">
                        <h4>Character Rewards</h4>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th width="70%">Reward</th>
                                    <th width="30%">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="character-rewards">
                            </tbody>
                        </table>
                        <div class="text-right">
                            <a href="#" class="btn btn-outline-primary btn-sm add-reward">Add Reward</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <table>
        <tr class="character-reward-row">
            <td>
                {!! Form::select('currency_id[]', $characterCurrencies, 0, ['class' => 'form-control currency-id']) !!}
            </td>
            <td class="d-flex align-items-center">
                {!! Form::text('quantity[]', 0, ['class' => 'form-control mr-2 quantity']) !!}
                <a href="#" class="remove-reward d-block"><i class="fas fa-times text-muted"></i></a>
            </td>
        </tr>
    </table>
</div>


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
<script>
    $(document).ready(function() {
        var $prompt = $('#prompt');
        var $addCharacter = $('#addCharacter');
        var $components = $('#components');
        var $rewards = $('#rewards');
        var $characters = $('#characters');
        var $submitButton = $('#submitButton');
        var $confirmationModal = $('#confirmationModal');
        var $formSubmit = $('#formSubmit');
        var $submissionForm = $('#submissionForm');
        var count = 0;

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

        $addCharacter.on('click', function(e) {
            e.preventDefault();
            $clone = $components.find('.submission-character').clone();
            attachListeners($clone);
            $characters.append($clone);
            count++;
        });

        function attachListeners(node) {
            node.find('.character-code').on('change', function(e) {
                var $parent = $(this).parent().parent().parent().parent();
                $parent.find('.character-image-loaded').load('{{ url('submissions/new/character') }}/'+$(this).val(), function(response, status, xhr) {
                    $parent.find('.character-image-blank').addClass('hide');
                    $parent.find('.character-image-loaded').removeClass('hide');
                    $parent.find('.character-rewards').removeClass('hide');
                    updateRewardNames(node, node.find('.character-info').data('id'));
                });
            });
            node.find('.remove-character').on('click', function(e) {
                e.preventDefault();
                $(this).parent().parent().parent().remove();
            });
            node.find('.add-reward').on('click', function(e) {
                e.preventDefault();
                $clone = $components.find('.character-reward-row').clone();
                $clone.find('.remove-reward').on('click', function(e) {
                    e.preventDefault();
                    $(this).parent().parent().remove();
                });
                updateRewardNames($clone, node.find('.character-info').data('id'));
                $(this).parent().parent().find('.character-rewards').append($clone);
            });
        }

        function updateRewardNames(node, id) {
            node.find('.currency-id').attr('name', 'currency_id[' + id + '][]');
            node.find('.quantity').attr('name', 'quantity[' + id + '][]');
            console.log('currency_id[' + id + '][]');
        }

    });
</script>
@endsection