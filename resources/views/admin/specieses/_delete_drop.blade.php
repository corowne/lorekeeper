@if($drop)
    {!! Form::open(['url' => 'admin/data/character-drops/delete/'.$drops->id]) !!}

    <p>You are about to delete this character drop. This is not reversible. If traits and/or characters that have drops associated with this data exist, you will not be able to delete it.</p>
    <p>Are you sure you want to delete this drop?</p>

    <div class="text-right">
        {!! Form::submit('Delete Drop', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else 
    Invalid drop selected.
@endif