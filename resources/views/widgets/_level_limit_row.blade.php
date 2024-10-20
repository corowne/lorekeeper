<div id="limitRowData" class="hide">
    <table class="table table-sm">
        <tbody id="limitRow">
            <tr class="limit-row">
                <td>{!! Form::select('limit_type[]', ['Item' => 'Item', 'Currency' => 'Currency'], null, ['class' => 'form-control limit-type', 'placeholder' => 'Select limit Type']) !!}</td>
                <td class="limit-row-select"></td>
                <td>{!! Form::text('limit_quantity[]', 1, ['class' => 'form-control']) !!}</td>
                <td class="text-right"><a href="#" class="btn btn-danger remove-limit-button">Remove</a></td>
            </tr>
        </tbody>
    </table>
    {!! Form::select('limit_id[]', $items, null, ['class' => 'form-control limit-item-select', 'placeholder' => 'Select Item']) !!}
    {!! Form::select('limit_id[]', $currencies, null, ['class' => 'form-control limit-currency-select', 'placeholder' => 'Select Currency']) !!}
</div> 