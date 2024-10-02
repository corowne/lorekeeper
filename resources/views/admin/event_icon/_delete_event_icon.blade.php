@if ($eventIcon)
    {!! Form::open(['url' => 'admin/data/event-icon/delete/' . $eventIcon->id]) !!}

    <div class="text-right">
        {!! Form::submit('Delete Event Icon', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid event icon selected.
@endif
