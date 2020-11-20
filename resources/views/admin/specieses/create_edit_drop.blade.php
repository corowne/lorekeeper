@extends('admin.layout')

@section('admin-title') {{ $drop->id ? 'Edit' : 'Create' }} Character Drop @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Character Drops' => 'admin/data/character-drops', ($drop->id ? 'Edit' : 'Create').' Drop Data' => $drop->id ? 'admin/data/character-drops/edit/'.$drop->id : 'admin/data/character-drops/create']) !!}

<h1>{{ $drop->id ? 'Edit' : 'Create' }} Character Drop Data
    @if($drop->id)
        <a href="#" class="btn btn-danger float-right delete-drop-button">Delete Drop</a>
    @endif
</h1>

{!! Form::open(['url' => $drop->id ? 'admin/data/character-drops/edit/'.$drop->id : 'admin/data/character-drops/create']) !!}

<h2>Basic Information</h2>

<div class="form-group">
    {!! Form::label('Species') !!}
    {!! Form::select('species_id', $specieses, $drop->species_id, ['class' => 'form-control']) !!}
</div>

<h4>Groups</h4>
<p>
    Groups into which characters within this species may be sorted. Characters of this species can be assigned a group or be randomly assigned one upon creation, or may be manually assigned or reassigned a group after the fact.
</p>
<div class="float-right mb-3">
    <a href="#" class="btn btn-info" id="addLoot">Add Parameter</a>
</div>
<table class="table table-sm" id="lootTable">
    <thead>
        <tr>
            <th width="25%">Label {!! add_help('This label will be shown to users.') !!}</th>
            <th width="10%">Weight {!! add_help('A higher weight means a group is more likely to be rolled. Weights have to be integers above 0 (round positive number, no decimals) and do not have to add up to be a particular number.') !!}</th>
            <th width="20%">Chance</th>
            <th width="10%"></th>
        </tr>
    </thead>
    <tbody id="lootTableBody">
        @if($drop->id)
            @foreach($drop->parameters as $label=>$weight)
                <tr class="drop-row">
                    <td class="drop-row-select">{!! Form::text('label[]', $label, ['class' => 'form-control']) !!}</td>
                    <td class="drop-row-weight">{!! Form::text('weight[]', $weight, ['class' => 'form-control drop-weight']) !!}</td>
                    <td class="drop-row-chance"></td>
                    <td class="text-right"><a href="#" class="btn btn-danger remove-drop-button">Remove</a></td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>

<h4>Drop Frequency</h4>
Select how often drops should occur.
<div class="d-flex my-2">
    {!! Form::number('drop_frequency', $drop->data['frequency']['frequency'], ['class' => 'form-control mr-2', 'placeholder' => 'Drop Frequency']) !!}
    {!! Form::select('drop_interval', ['hour' => 'Hour', 'day' => 'Day', 'month' => 'Month', 'year' => 'Year'], $drop->data['frequency']['interval'], ['class' => 'form-control mr-2 default item-select', 'placeholder' => 'Drop Interval']) !!}
</div>

<div class="form-group">
    {!! Form::checkbox('is_active', 1, $drop->id ? $drop->data['is_active'] : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
    {!! Form::label('is_active', 'Is Active', ['class' => 'form-check-label ml-3']) !!} {!! add_help('Whether or not drops for this species are active. Impacts subtypes as well.') !!}
</div>

@if($drop->id)
    <h2>Dropped Items</h2>
    Select an item for each group of this species to drop, and/or for each group to drop per subtype of this species. Leave the item field blank to disable drops for the group.
    <div class="card card-body my-2">
        @foreach($drop->parameters as $label=>$weight)
            <div class="mb-2">
                <h5>{{ $label }}</h5>
                <div class="form-group">
                    {!! Form::label('Item and Min/Max Quantity Dropped') !!} {!! add_help('Select an item for this group to drop, and the minimum and maximum quantity that should be dropped. If only one quantity is set, or they are both the same number, the same quantity will be dropped each time.') !!}
                    <div id="itemList">
                        <div class="d-flex mb-2">
                            {!! Form::select('item_id[species]['.$label.']', $items, isset($drop->data['items']['species'][$label]) ? $drop->data['items']['species'][$label]['item_id'] : null, ['class' => 'form-control mr-2 default item-select', 'placeholder' => 'Select Item']) !!}
                            {!! Form::number('min_quantity[species]['.$label.']', isset($drop->data['items']['species'][$label]) ? $drop->data['items']['species'][$label]['min'] : null, ['class' => 'form-control mr-2', 'placeholder' => 'Minimum Quantity']) !!}
                            {!! Form::number('max_quantity[species]['.$label.']', isset($drop->data['items']['species'][$label]) ?  $drop->data['items']['species'][$label]['max'] : null, ['class' => 'form-control mr-2', 'placeholder' => 'Maximum Quantity']) !!}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <h3>Subtypes</h3>
    @if($drop->species->subtypes->count())
        @foreach($drop->species->subtypes as $subtype)
            <div class="card card-body mb-2">
                <div class="card-title">
                    <h4>{{ $subtype->name.' '.$subtype->species->name }}</h4>
                </div>
                @foreach($drop->parameters as $label=>$weight)
                    <div class="mb-2">
                        <h5>{{ $label }}</h5>
                        <div class="form-group">
                            {!! Form::label('Item and Min/Max Quantity Dropped') !!} {!! add_help('Select an item for this group to drop, and the minimum and maximum quantity that should be dropped. If only one quantity is set, or they are both the same number, the same quantity will be dropped each time.') !!}
                            <div id="itemList">
                                <div class="d-flex mb-2">
                                    {!! Form::select('item_id['.$subtype->id.']['.$label.']', $items, isset($drop->data['items'][$subtype->id][$label]) ? $drop->data['items'][$subtype->id][$label]['item_id'] : null, ['class' => 'form-control mr-2 default item-select', 'placeholder' => 'Select Item']) !!}
                                    {!! Form::number('min_quantity['.$subtype->id.']['.$label.']', isset($drop->data['items'][$subtype->id][$label]) ? $drop->data['items'][$subtype->id][$label]['min'] : null, ['class' => 'form-control mr-2', 'placeholder' => 'Minimum Quantity']) !!}
                                    {!! Form::number('max_quantity['.$subtype->id.']['.$label.']', isset($drop->data['items'][$subtype->id][$label]) ? $drop->data['items'][$subtype->id][$label]['max'] : null, ['class' => 'form-control mr-2', 'placeholder' => 'Maximum Quantity']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    @else
        <p>No subtypes found for this species.</p>
    @endif
@endif

<div class="text-right">
    {!! Form::submit($drop->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

<div id="dropRowData" class="hide">
    <table class="table table-sm">
        <tbody id="dropRow">
            <tr class="drop-row">
                <td class="drop-row-select">{!! Form::text('label[]', null, ['class' => 'form-control']) !!}</td>
                <td class="drop-row-weight">{!! Form::text('weight[]', 1, ['class' => 'form-control drop-weight']) !!}</td>
                <td class="drop-row-chance"></td>
                <td class="text-right"><a href="#" class="btn btn-danger remove-drop-button">Remove</a></td>
            </tr>
        </tbody>
    </table>
</div>

@endsection

@section('scripts')
@parent
<script>
$( document ).ready(function() {    
    $('.delete-drop-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/data/character-drops/delete') }}/{{ $drop->id }}", 'Delete Drop');
    });

    var $lootTable  = $('#lootTableBody');
    var $dropRow = $('#dropRow').find('.drop-row');
    var $itemSelect = $('#dropRowData').find('.item-select');
    var $currencySelect = $('#dropRowData').find('.currency-select');
    var $tableSelect = $('#dropRowData').find('.table-select');
    var $noneSelect = $('#dropRowData').find('.none-select');

    refreshChances();
    $('#lootTableBody .selectize').selectize();
    attachRemoveListener($('#lootTableBody .remove-drop-button'));

    $('#addLoot').on('click', function(e) {
        e.preventDefault();
        var $clone = $dropRow.clone();
        $lootTable.append($clone);
        attachRewardTypeListener($clone.find('.reward-type'));
        attachRemoveListener($clone.find('.remove-drop-button'));
        attachWeightListener($clone.find('.drop-weight'));
        refreshChances();
    });

    $('.reward-type').on('change', function(e) {
        var val = $(this).val();
        var $cell = $(this).parent().find('.drop-row-select');

        var $clone = null;
        if(val == 'Item') $clone = $itemSelect.clone();
        else if (val == 'Currency') $clone = $currencySelect.clone();
        else if (val == 'LootTable') $clone = $tableSelect.clone();
        else if (val == 'None') $clone = $noneSelect.clone();

        $cell.html('');
        $cell.append($clone);
    });

    function attachRewardTypeListener(node) {
        node.on('change', function(e) {
            var val = $(this).val();
            var $cell = $(this).parent().parent().find('.drop-row-select');

            var $clone = null;
            if(val == 'Item') $clone = $itemSelect.clone();
            else if (val == 'Currency') $clone = $currencySelect.clone();
            else if (val == 'LootTable') $clone = $tableSelect.clone();
            else if (val == 'None') $clone = $noneSelect.clone();

            $cell.html('');
            $cell.append($clone);
            $clone.selectize();
        });
    }

    function attachRemoveListener(node) {
        node.on('click', function(e) {
            e.preventDefault();
            $(this).parent().parent().remove();
            refreshChances();
        });
    }

    function attachWeightListener(node) {
        node.on('change', function(e) {
            refreshChances();
        });
    }

    function refreshChances() {
        var total = 0;
        var weights = [];
        $('#lootTableBody .drop-weight').each(function( index ) {
            var current = parseInt($(this).val());
            total += current;
            weights.push(current);
        });

        
        $('#lootTableBody .drop-row-chance').each(function( index ) {
            var current = (weights[index] / total) * 100;
            $(this).html(current.toString() + '%');
        });
    }

    $('.default.item-select').selectize();
});
    
</script>
@endsection