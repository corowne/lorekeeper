@if($stock)
    {!! Form::open(['url' => 'usershops/stock/remove/'.$stock->id]) !!}
    {{ Form::hidden('user_shop_id', $shop->id) }}
    {{ Form::hidden('ids', $stock->id) }}
    {!! Form::selectRange('quantities', 1, $stock->quantity, 1, ['class' => 'quantity-select', 'type' => 'number', 'style' => 'min-width:40px;']) !!}
    <p>You are about to remove the stock <strong>{{ $stock->item->name }}</strong>.</p>
    <p>Are you sure you want to remove <strong>{{ $stock->item->name }}</strong>? This item will be returned to your inventory.</p>

    <div class="text-right">
        {!! Form::submit('Remove Stock', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else 
    Invalid stock selected.
@endif

