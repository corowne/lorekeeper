@if($forum)
    {!! Form::open(['url' => 'admin/forums/delete/'.$forum->id]) !!}

    <p>You are about to delete the forum <strong>{{ $forum->name }}</strong>. This is not reversible.</p>
    <p>This will not delete any of the threads or posts within.</p>

    <div class="form-group">
        {!! Form::label('child_boards', 'Delete children of this forum?', ['class' => 'form-check-label ml-3']) !!}
        {!! Form::checkbox('child_boards', 1, 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'data-on' => 'Delete Child Forums', 'data-off' => 'Don\'t Delete Child Forums']) !!}
    </div>

    <p>Are you sure you want to delete <strong>{{ $forum->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Forum', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid forum selected.
@endif
