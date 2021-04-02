@if($level)
    {!! Form::open(['url' => 'admin/levels/character/delete/'.$level->id]) !!}

    <p>You are about to delete the level <strong>{{ $level->name }}</strong>. This is not reversible. If a user/character has reached this level you will be unable to delete it.</p>
    <p>Are you sure you want to delete <strong>{{ $level->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Level', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else 
    Invalid level selected.
@endif