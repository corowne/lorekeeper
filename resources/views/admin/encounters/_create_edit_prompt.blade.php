{!! Form::open([
    'url' => 'admin/data/encounters/edit/' . $encounter->id . '/prompts/' . ($prompt->id ? 'edit/' . $prompt->id : 'create'),
]) !!}


<h3>Basic Information</h3>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('Name') !!}{!! add_help('This is what the user will see as an option when seeing an encounter with this prompt.') !!}
            {!! Form::text('name', $prompt->name, ['class' => 'form-control']) !!}
        </div>
    </div>
    @if ($prompt->id)
        <div class="col-md-6">
            <div class="form-check">
                {!! Form::checkbox('delete', 1, false, ['class' => 'form-check-input']) !!}
                {!! Form::label('delete', 'Delete Option', ['class' => 'form-check-label']) !!}
            </div>
    @endif
</div>
</div>

<div class="form-group">
    {!! Form::label('Result Type (Required)') !!}{!! add_help('Choose whether or not the user sees this result as a failure, success, or neutral. This colors the result box.') !!}
    {!! Form::select('result_type', ['failure' => 'Failure', 'success' => 'Success', 'neutral' => 'Neutral'], isset($prompt->extras['result_type']) ? $prompt->extras['result_type'] : '', [
        'class' => 'form-control',
        'placeholder' => 'Select a Type',
    ]) !!}
</div>

<div class="form-group">
    {!! Form::label('result') !!}{!! add_help('Describe the result of this encounter. This will be displayed as a success/error message when this option is selected.') !!}
    {!! Form::textarea('result', $prompt->result, [
        'class' => 'form-control wysiwyg',
        'id' => uniqid('result-', true),
    ]) !!}
</div>

<h3>Energy Alterations (Optional)</h3>
<p>You can allow this prompt to alter the user's encounter energy when it is selected.</p>
<p>Select if it adds or subtracts, and mow much is taken or given.</p>
<p>Both fields must be filled out for energy alteration to take place.</p>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('Math Type') !!}
            {!! Form::select('math_type', ['add' => 'Add', 'subtract' => 'Subtract'], isset($prompt->extras['math_type']) ? $prompt->extras['math_type'] : '', ['class' => 'form-control', 'placeholder' => 'Select a Modifier']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('Energy Value') !!}{!! add_help('Amount of energy to be given or taken.') !!}
            {!! Form::number('energy_value', isset($prompt->extras['energy_value']) ? $prompt->extras['energy_value'] : '', [
                'class' => 'form-control',
            ]) !!}
        </div>
    </div>
</div>
<hr>

<h3>Rewards</h3>
<p>You can add loot tables containing any kind of currencies (both user- and character-attached), but be sure to keep
    track of which are being distributed! Character-only currencies cannot be given to users.</p>
@include('widgets._loot_select', [
    'loots' => $prompt->rewards,
    'showLootTables' => true,
    'showRaffles' => true,
])
<hr>
<h3>Restrict action</h3>
<p>Users/characters must obtain all these requirements to be able to attempt the action.</p>
<p>Remember to keep track of who can hold what, or you may end up with an impossible set of actions.</p>

<div class="text-right mb-3">
    <a href="#" class="btn btn-outline-info" id="addLimit">Add Limit</a>
</div>
<table class="table table-sm" id="limitTable">
    <thead>
        <tr>
            <th width="35%">Limit Type</th>
            <th width="35%">Limit</th>
            <th width="10%"></th>
        </tr>
    </thead>
    <tbody id="limitTableBody">
        @if ($prompt->limits)
            @foreach ($prompt->limits as $limit)
                <tr class="limit-row">
                    <td>{!! Form::select('item_type[]', ['Item' => 'Item', 'Currency' => 'Currency'], $limit->item_type, [
                        'class' => 'form-control reward-type',
                        'placeholder' => 'Select limit Type',
                    ]) !!}</td>
                    <td class="limit-row-select">
                        @if ($limit->item_type == 'Item')
                            {!! Form::select('item_id[]', $items, $limit->item_id, [
                                'class' => 'form-control item-select selectize',
                                'placeholder' => 'Select Item',
                            ]) !!}
                        @elseif($limit->item_type == 'Currency')
                            {!! Form::select('item_id[]', $currencies, $limit->item_id, [
                                'class' => 'form-control currency-select selectize',
                                'placeholder' => 'Select Currency',
                            ]) !!}
                        @endif
                    </td>
                    <td class="text-right"><a href="#" class="btn btn-danger remove-limit-button">Remove</a></td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>

<div class="text-right">
    {!! Form::submit($prompt->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

@include('widgets._loot_select_row', [
    'items' => $items,
    'currencies' => $currencies,
    'tables' => $tables,
    'raffles' => $raffles,
    'showLootTables' => true,
    'showRaffles' => true,
])

<div id="limitRowData" class="hide">
    <table class="table table-sm">
        <tbody id="limitRow">
            <tr class="limit-row">
                <td>
                    {!! Form::select('item_type[]', ['Item' => 'Item', 'Currency' => 'Currency'], null, [
                        'class' => 'form-control reward-type',
                        'placeholder' => 'Select limit Type',
                    ]) !!}
                </td>
                <td class="limit-row-select"></td>
                <td class="text-right"><a href="#" class="btn btn-danger remove-limit-button">Remove</a></td>
            </tr>
        </tbody>
    </table>
    {!! Form::select('item_id[]', $items, null, [
        'class' => 'form-control item-select',
        'placeholder' => 'Select Item',
    ]) !!}
    {!! Form::select('item_id[]', $currencies, null, [
        'class' => 'form-control currency-select',
        'placeholder' => 'Select Currency',
    ]) !!}
</div>

@include('js._loot_js', ['showLootTables' => true, 'showRaffles' => true])

<script>
    $(document).ready(function() {
        @include('js._modal_wysiwyg')

        var $limitTable = $('#limitTableBody');
        var $limitRow = $('#limitRow').find('.limit-row');
        var $itemSelect = $('#limitRowData').find('.item-select');
        var $currencySelect = $('#limitRowData').find('.currency-select');


        $('#limitTableBody .selectize').selectize();
        attachRewardTypeListener($('#limitTableBody .reward-type'));
        attachRemoveListener($('#limitTableBody .remove-limit-button'));

        $('#addLimit').on('click', function(e) {
            e.preventDefault();
            var $clone = $limitRow.clone();
            $limitTable.append($clone);
            attachRewardTypeListener($clone.find('.reward-type'));
            attachRemoveListener($clone.find('.remove-limit-button'));
        });

        $('.reward-type').on('change', function(e) {
            var val = $(this).val();
            var $cell = $(this).parent().find('.limit-row-select');

            var $clone = null;
            if (val == 'Item') $clone = $itemSelect.clone();
            else if (val == 'Currency') $clone = $currencySelect.clone();


            $cell.html('');
            $cell.append($clone);
        });

        function attachRewardTypeListener(node) {
            node.on('change', function(e) {
                var val = $(this).val();
                var $cell = $(this).parent().parent().find('.limit-row-select');

                var $clone = null;
                if (val == 'Item') $clone = $itemSelect.clone();
                else if (val == 'Currency') $clone = $currencySelect.clone();

                $cell.html('');
                $cell.append($clone);
                $clone.selectize();
            });
        }

        function attachRemoveListener(node) {
            node.on('click', function(e) {
                e.preventDefault();
                $(this).parent().parent().remove();
            });
        }
    });
</script>
