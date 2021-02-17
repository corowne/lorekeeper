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
        @if($loots)
            @foreach($loots as $loot)
                <tr class="limit-row">
                    <td>{!! Form::select('limit_type[]', ['Item' => 'Item', 'Currency' => 'Currency'], $loot->limit_type, ['class' => 'form-control limit-type', 'placeholder' => 'Select limit Type']) !!}</td>
                    <td class="limit-row-select">
                        @if($loot->limit_type == 'Item')
                            {!! Form::select('limit_id[]', $items, $loot->limit_id, ['class' => 'form-control limit-item-select selectize', 'placeholder' => 'Select Item']) !!}
                        @elseif($loot->limit_type == 'Currency')
                            {!! Form::select('limit_id[]', $currencies, $loot->limit_id, ['class' => 'form-control limit-currency-select selectize', 'placeholder' => 'Select Currency']) !!}
                        @endif
                    </td>
                    <td>{!! Form::text('limit_quantity[]', $loot->quantity, ['class' => 'form-control']) !!}</td>
                    <td class="text-right"><a href="#" class="btn btn-danger remove-limit-button">Remove</a></td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table> 