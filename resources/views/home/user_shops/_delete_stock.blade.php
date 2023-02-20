@if($stock)
    {!! Form::open(['url' => 'usershops/stock/remove/'.$stock->id]) !!}

    <p>You are about to delete the stock <strong>{{ $stock->item->name }}</strong>.</p>
    <p>Are you sure you want to delete <strong>{{ $stock->item->name }}</strong>? This item will be returned to your inventory.</p>

    <div class="text-right">
        {!! Form::submit('Remove Stock', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else 
    Invalid stock selected.
@endif