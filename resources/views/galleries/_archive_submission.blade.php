@if($submission)
    {!! Form::open(['url' => 'galleries/archive/'.$submission->id]) !!}

    <p>You are about to archive the submission <strong>{{ $submission->title }}</strong>. This is reversible; you will be able to unarchive the submission at any time. Archiving a submission hides it from view by other users, but not staff.</p>
    <p>Are you sure you want to archive <strong>{{ $submission->title }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Archive Submission', ['class' => 'btn btn-warning']) !!}
    </div>

    {!! Form::close() !!}
@else 
    Invalid submission selected.
@endif