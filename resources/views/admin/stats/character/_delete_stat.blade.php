@if($stat)
    {!! Form::open(['url' => 'admin/stats/delete/'.$stat->id]) !!}

    <p>You are about to delete the stat <strong>{{ $stat->name }}</strong>. This is not reversible. Any character info with this stat will not be retrievable.</p>
    <p>Are you sure you want to delete <strong>{{ $stat->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Stat', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else 
    Invalid stat selected.
@endif