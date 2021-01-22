<div class="text-right mb-3">
    <a href="#" class="btn btn-outline-info" id="addLoot">Add Limit</a>
</div>
<table class="table table-sm" id="lootTable">
    <thead>
        <tr>
            <th width="35%">Limit Type</th>
            <th width="35%">Limit</th>
            <th width="20%">Quantity</th>
            <th width="10%"></th>
        </tr>
    </thead>
    <tbody id="lootTableBody">
        @if($loots)
            @foreach($loots as $loot)
                <tr class="loot-row">
                    <td>{!! Form::select('limit_type[]', ['Item' => 'Item', 'Currency' => 'Currency', 'Recipe' => 'Recipe'], $loot->limit_type, ['class' => 'form-control reward-type', 'placeholder' => 'Select limit Type']) !!}</td>
                    <td class="loot-row-select">
                        @if($loot->limit_type == 'Item')
                            {!! Form::select('limit_id[]', $items, $loot->limit_id, ['class' => 'form-control item-select selectize', 'placeholder' => 'Select Item']) !!}
                        @elseif($loot->limit_type == 'Currency')
                            {!! Form::select('limit_id[]', $currencies, $loot->limit_id, ['class' => 'form-control currency-select selectize', 'placeholder' => 'Select Currency']) !!}
                        @elseif($showRecipes && $loot->limit_type == 'Recipe')
                            {!! Form::select('limit_id[]', $recipes, $loot->limit_id, ['class' => 'form-control recipe-select selectize', 'placeholder' => 'Select Recipe']) !!}
                        @endif
                    </td>
                    <td>{!! Form::text('limit_quantity[]', $loot->quantity, ['class' => 'form-control']) !!}</td>
                    <td class="text-right"><a href="#" class="btn btn-danger remove-loot-button">Remove</a></td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>