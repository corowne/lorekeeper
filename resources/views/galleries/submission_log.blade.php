@extends('galleries.layout')

@section('gallery-title') {{ $submission->title }} Log @endsection

@section('gallery-content')
{!! breadcrumbs(['gallery' => 'gallery', $submission->gallery->displayName => 'gallery/'.$submission->gallery->id, $submission->title => 'gallery/view/'.$submission->id, 'Log Details' => 'gallery/queue/'.$submission->id ]) !!}

<h1>Log Details
    <span class="float-right badge badge-{{ $submission->status == 'Pending' ? 'secondary' : ($submission->status == 'Accepted' ? 'success' : 'danger') }}">{{ $submission->collaboratorApproved ? $submission->status : 'Pending Collaborator Approval' }}</span>
</h1>

@include('galleries._queue_submission', ['key' => 0])

<div class="row">
    <div class="col-md">
        <div class="card">
            <div class="card-header">
                <h4>Staff Comments{!! isset($submission->staff_id) ? ' - '.$submission->staff->displayName : '' !!}</h4>
                {!! Auth::user()->hasPower('staff_comments') ? '(Visible to '.$submission->credits.')' : '' !!}
            </div>
            <div class="card-body">
                @if(Auth::user()->hasPower('staff_comments'))
                    {!! Form::open(['url' => 'admin/gallery/edit/'.$submission->id.'/comment']) !!}
                        <div class="form-group">
                            {!! Form::label('staff_comments', 'Staff Comments') !!}
                            {!! Form::textarea('staff_comments', $submission->staff_comments, ['class' => 'form-control wysiwyg']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::checkbox('alert_user', 1, true, ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'data-onstyle' => 'danger']) !!}
                            {!! Form::label('alert_user', 'Notify User', ['class' => 'form-check-label ml-3']) !!} {!! add_help('This will send a notification to the user that the staff comments on their submission have been edited.') !!}
                        </div>
                        <div class="text-right">
                            {!! Form::submit('Edit Comments', ['class' => 'btn btn-primary']) !!}
                        </div>
                    {!! Form::close() !!}
                @else
                    {!! isset($submission->parsed_staff_comments) ? $submission->parsed_staff_comments : '<i>No comments provided.</i>' !!}
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        @if(Auth::user()->hasPower('manage_submissions') && $submission->collaboratorApproved)
            <div class="card mb-4">
                <div class="card-header">
                    <h5>[Admin] Vote Info</h5>
                </div>
                <div class="card-body">
                    @if(isset($submission->vote_data) && $submission->voteData->count())
                        @foreach($submission->voteData as $voter=>$vote)
                            <li>
                                {!! App\Models\User\User::find($voter)->displayName !!} {{ $voter == Auth::user()->id ? '(you)' : '' }}: <span {!! $vote == 2 ? 'class="text-success">Accept' : 'class="text-danger">Reject' !!}</span>
                            </li>
                        @endforeach
                    @else
                        <p>No votes have been cast yet!</p>
                    @endif
                </div>
            </div>
        @endif
        @if(Settings::get('gallery_submissions_reward_currency') && $submission->gallery->currency_enabled)
            <div class="card">
                <div class="card-header">
                    <h5>{!! $currency->displayName !!} Award Info</h5>
                </div>
                <div class="card-body">
                    @if($submission->status == 'Accepted')
                        @if(!$submission->is_valued)
                            @if(Auth::user()->hasPower('manage_submissions'))
                                <p>Enter in the amount of {{ $currency->name }} that {{ $submission->collaborators->count() ? 'each collaborator' : 'the submitting user'}}{{ $submission->participants->count() ? ' and any participants' : '' }} should receive. The suggested amount has been pre-filled for you based on the provided form responses, but this is only a guideline based on user input and should be verified and any adjustments made as necessary.</p>
                                {!! Form::open(['url' => 'admin/gallery/edit/'.$submission->id.'/value']) !!}
                                    @if(!$submission->collaborators->count() || $submission->collaborators->where('user_id', $submission->user_id)->first() == null)
                                        <div class="form-group">    
                                            {!! Form::label($submission->user->name) !!}:
                                            {!! Form::number('value[submitted]['.$submission->user->id.']', round(($submission->characters->count() ? round($submission->data['total'] * $submission->characters->count()) : $submission->data['total']) / ($submission->collaborators->count() ? $submission->collaborators->count() : '1')), ['class' => 'form-control']) !!}
                                        </div>
                                    @endif
                                    @if($submission->collaborators->count())
                                        @foreach($submission->collaborators as $key=>$collaborator)
                                            <div class="form-group">    
                                                {!! Form::label($collaborator->user->name.' ('.$collaborator->data.')') !!}:
                                                {!! Form::number('value[collaborator]['.$collaborator->user->id.']', round(($submission->characters->count() ? round($submission->data['total'] * $submission->characters->count()) : $submission->data['total']) / ($submission->collaborators->count() ? $submission->collaborators->count() : '1')), ['class' => 'form-control']) !!}
                                            </div>
                                        @endforeach
                                    @endif
                                    @if($submission->participants->count())
                                        @foreach($submission->participants as $key=>$participant)
                                            <div class="form-group">    
                                                {!! Form::label($participant->user->name.' ('.$participant->displayType.')') !!}:
                                                {!! Form::number('value[participant]['.$participant->user->id.']', $participant->type == 'Comm' ? round(($submission->characters->count() ? round($submission->data['total'] * $submission->characters->count()) : $submission->data['total']) / ($submission->collaborators->count() ? $submission->collaborators->count() : '1')/2) : 0, ['class' => 'form-control']) !!}
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="form-group">
                                        {!! Form::checkbox('ineligible', 1, false, ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'data-onstyle' => 'danger']) !!}
                                        {!! Form::label('ineligible', 'Inelegible/Award No Currency', ['class' => 'form-check-label ml-3']) !!} {!! add_help('When on, this will mark the submission as valued, but will not award currency to any of the users listed.') !!}
                                    </div>
                                    <div class="text-right">
                                        {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
                                    </div>
                                {!! Form::close() !!}
                            @else
                                <p>This submission hasn't been evaluated yet. You'll receive a notification once it has!</p>
                            @endif
                        @else
                            @if(isset($submission->data['ineligible']) && $submission->data['ineligible'] == 1)
                                <p>This submission has been evaluated as ineligible for {{ $currency->name }} rewards.</p>
                            @else
                                <p>{{ $currency->name }} has been awarded for this submission.</p>
                                <p>
                                    @if(isset($submission->data['value']['submitted']))
                                        {!! $submission->user->displayName !!}: {!! $currency->display($submission->data['value']['submitted'][$submission->user->id]) !!}
                                        <br/>
                                    @endif
                                    @if($submission->collaborators->count())
                                        @foreach($submission->collaborators as $collaborator)
                                            {!! $collaborator->user->displayName !!} ({{ $collaborator->data }}): {!! $currency->display($submission->data['value']['collaborator'][$collaborator->user->id]) !!}
                                        <br/>
                                        @endforeach
                                    @endif
                                    @if($submission->participants->count())
                                        @foreach($submission->participants as $participant)
                                            {!! $participant->user->displayName !!} ({{ $participant->displayType }}): {!! $currency->display($submission->data['value']['participant'][$participant->user->id]) !!}
                                        <br/>
                                        @endforeach
                                    @endif
                                </p>
                            @endif
                        @endif
                    @else
                        <p>This submission is not eligible for currency awards{{ $submission->status == 'Pending' ? ' yet-- it must be accepted first' : '' }}.</p>
                    @endif
                    <hr/>
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
                        <p class="text-center">
                            <strong>Calculated Total:</strong> {{ $submission->data['total'] }}
                            @if($submission->characters->count())
                                 ・ <strong> Times {{ $submission->characters->count() }} Characters:</strong> {{ round($submission->data['total'] * $submission->characters->count()) }}
                            @endif
                            @if($submission->collaborators->count())
                                <br/><strong>Divided by {{ $submission->collaborators->count() }} Collaborators:</strong> {{ round($submission->data['total'] / $submission->collaborators->count()) }}
                                @if($submission->characters->count())
                                    ・ <strong> Times {{ $submission->characters->count() }} Characters:</strong> {{ round(round($submission->data['total'] * $submission->characters->count()) / $submission->collaborators->count()) }}
                                @endif
                            @endif
                            <br/>For a suggested {!! $currency->display(
                                round(
                                    ($submission->characters->count() ? round($submission->data['total'] * $submission->characters->count()) : $submission->data['total']) / ($submission->collaborators->count() ? $submission->collaborators->count() : '1')
                                )
                            ) !!}{{ $submission->collaborators->count() ? ' per collaborator' : ''}}
                        </p>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<?php $galleryPage = true; 
$sideGallery = $submission->gallery ?>

@endsection