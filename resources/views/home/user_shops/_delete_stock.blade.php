@if($stock)
    {!! Form::open(['url' => 'usershops/stock/remove/'.$stock->id]) !!}
    {{ Form::hidden('user_shop_id', $shop->id) }}
        <table class="table table-sm">
            <thead class="thead">
                <tr class="d-flex">
                    <th class="col-2">Quantity</th>
                    <th class="col-1"><i class="fas fa-lock invisible"></i></th>
                </tr>
            </thead>
            <tbody>
                    <tr id ="stock{{ $stock->id }}">
                        <td class="col-1">{!! Form::checkbox('ids[]', $stock->id, false, ['class' => 'item-check', 'onclick' => 'updateQuantities(this)']) !!}</td>
                        <td class="col-2">{!! Form::selectRange('', 1, $stock->quantity, 1, ['class' => 'quantity-select', 'type' => 'number', 'style' => 'min-width:40px;']) !!}</td>
                    </tr>
            </tbody>
        </table>
    <p>You are about to remove the stock <strong>{{ $stock->item->name }}</strong>.</p>
    <p>Are you sure you want to remove <strong>{{ $stock->item->name }}</strong>? This item will be returned to your inventory.</p>

    <div class="text-right">
        {!! Form::submit('Remove Stock', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}

    <script>
    function updateQuantities($checkbox) {
        var $rowId = "#stock" + $checkbox.value
        $($rowId).find('.quantity-select').prop('name', $checkbox.checked ? 'quantities[]' : '')
    }
</script>
@else 
    Invalid stock selected.
@endif


