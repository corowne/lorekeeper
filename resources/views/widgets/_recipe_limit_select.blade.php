<div class="text-right mb-3">
    <a href="#" class="btn btn-outline-info" id="addLimit">Add Limit</a>
</div>
<table class="table table-sm" id="limitTable">
    <thead>
        <tr>
            <th width="35%">Limit Type</th>
            <th width="35%">Limit</th>
            <th width="20%">Quantity</th>
            <th width="10%"></th>
        </tr>
    </thead>
    <tbody id="limitTableBody">
        @if($limits)
            @foreach($limits as $limit)
                <tr class="limit-row">
                    <td>{!! Form::select('limit_type[]', ['Item' => 'Item', 'Currency' => 'Currency', 'Recipe' => 'Recipe'], $limit->limit_type, ['class' => 'form-control reward-type', 'placeholder' => 'Select limit Type']) !!}</td>
                    <td class="limit-row-select">
                        @if($limit->limit_type == 'Item')
                            {!! Form::select('limit_id[]', $items, $limit->limit_id, ['class' => 'form-control item-select selectize', 'placeholder' => 'Select Item']) !!}
                        @elseif($limit->limit_type == 'Currency')
                            {!! Form::select('limit_id[]', $currencies, $limit->limit_id, ['class' => 'form-control currency-select selectize', 'placeholder' => 'Select Currency']) !!}
                        @elseif($showRecipes && $limit->limit_type == 'Recipe')
                            {!! Form::select('limit_id[]', $recipes, $limit->limit_id, ['class' => 'form-control recipe-select selectize', 'placeholder' => 'Select Recipe']) !!}
                        @endif
                    </td>
                    <td>{!! Form::text('limit_quantity[]', $limit->quantity, ['class' => 'form-control']) !!}</td>
                    <td class="text-right"><a href="#" class="btn btn-danger remove-limit-button">Remove</a></td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>