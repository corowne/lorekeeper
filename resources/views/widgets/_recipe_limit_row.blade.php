<div id="limitRowData" class="hide">
    <table class="table table-sm">
        <tbody id="limitRow">
            <tr class="limit-row">
                <td>{!! Form::select('limit_type[]', ['Item' => 'Item', 'Currency' => 'Currency', 'Recipe' => 'Recipe'], null, ['class' => 'form-control reward-type', 'placeholder' => 'Select limit Type']) !!}</td>
                <td class="limit-row-select"></td>
                <td>{!! Form::text('limit_quantity[]', 1, ['class' => 'form-control']) !!}</td>
                <td class="text-right"><a href="#" class="btn btn-danger remove-limit-button">Remove</a></td>
            </tr>
        </tbody>
    </table>
    {!! Form::select('limit_id[]', $items, null, ['class' => 'form-control item-select', 'placeholder' => 'Select Item']) !!}
    {!! Form::select('limit_id[]', $currencies, null, ['class' => 'form-control currency-select', 'placeholder' => 'Select Currency']) !!}
    {!! Form::select('limit_id[]', $recipes, null, ['class' => 'form-control recipe-select', 'placeholder' => 'Select Recipe']) !!}
</div>