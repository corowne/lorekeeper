@if ($eventIcon)
    {!! Form::open(['url' => 'admin/data/event-icon/delete/' . $eventIcon->id]) !!}

    <div class="text-right">
        {!! Form::submit('Delete EventIcon', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid eventicon selected.
@endif
