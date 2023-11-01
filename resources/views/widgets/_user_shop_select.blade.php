{!! Form::hidden('shop_id', $shop->id) !!}
<table class="table table-sm" id="stockTable">
    <thead>
        <tr>
            <th width="15%">Item</th>
            <th width="15%">Visible?</th>
            <th width="30%">Cost/Currency</th>
            <th width="20%">Removal Quantity</th>
        </tr>
    </thead>
    <tbody id="stockTableBody">
        @foreach($shop->stock->where('quantity', '>', 0) as $stock)
            <tr class="stock-row">
                {!! Form::hidden('stock_id[]', $stock->id) !!}
                <td>
                    @if (isset($stock->item->image_url))
                        <img class="small-icon" src="{{ $stock->item->image_url }}" alt="{{ $stock->item->name }}">
                    @endif
                    {!! $stock->item->name !!}
                    @if (!$stock->is_visible)
                        <i class="fas fa-eye-slash mr-1"></i>
                    @endif
                    <a href="{{ url('user-shops/item-search?item_id=' . $stock->item->id) }}">
                        <i class="fas fa-search"></i>
                    </a>
                </td>
                <td>{!! Form::checkbox('is_visible[]', 1, $stock->is_visible ?? 1, [
                    'class' => 'form-check-input',
                    'data-toggle' => 'toggle',
                ]) !!}
                </td>
                <td>
                    <div class="row no-gutters">
                        <div class="col-4">
                            {!! Form::text('cost[]', $stock->cost, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-8">
                            {!! Form::select('currency_id[]', $currencies, $stock->currency_id, [
                                'class' => 'form-control currency-select selectize',
                                'placeholder' => 'Select Currency',
                            ]) !!}
                        </div>
                    </div>
                </td>
                <td class="col-5">{!! Form::selectRange('quantity[]', 0, $stock->quantity, 0, [
                    'class' => 'quantity-select',
                    'type' => 'number',
                    'style' => 'min-width:40px;',
                ]) !!} /{{ $stock->quantity }} </td>
            </tr>
        @endforeach
    </tbody>
</table>
