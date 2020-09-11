@extends('admin.layout')

@section('admin-title') {{ $drop->id ? 'Edit' : 'Create' }} Character Drop @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Character Drops' => 'admin/data/character-drops', ($drop->id ? 'Edit' : 'Create').' Drop' => $drop->id ? 'admin/data/character-drops/edit/'.$drop->id : 'admin/data/character-drops/create']) !!}

<h1>{{ $drop->id ? 'Edit' : 'Create' }} Character Drop
    @if($drop->id)
        <a href="#" class="btn btn-danger float-right delete-subtype-button">Delete Drop</a>
    @endif
</h1>

{!! Form::open(['url' => $drop->id ? 'admin/data/character-drops/edit/'.$drop->id : 'admin/data/character-drops/create']) !!}

<h3>Basic Information</h3>

<div class="form-group">
    {!! Form::label('Species') !!}
    {!! Form::select('species_id', $specieses, $drop->species_id, ['class' => 'form-control']) !!}
</div>

<h4>Parameters</h4>
<div class="float-right mb-3">
    <a href="#" class="btn btn-info" id="addLoot">Add Parameter</a>
</div>
    <p>Groups into which characters within this species may be sorted.</p>
<table class="table table-sm" id="lootTable">
    <thead>
        <tr>
            <th width="25%">Label {!! add_help('This label will be shown to users.') !!}</th>
            <th width="10%">Weight {!! add_help('A higher weight means a reward is more likely to be rolled. Weights have to be integers above 0 (round positive number, no decimals) and do not have to add up to be a particular number.') !!}</th>
            <th width="20%">Chance</th>
            <th width="10%"></th>
        </tr>
    </thead>
    <tbody id="lootTableBody">
        @if($drop->id)
            @foreach($drop->parameters as $parameter)
                <tr class="drop-row">
                    <td class="drop-row-select">{!! Form::text('label[]', $parameter->label, ['class' => 'form-control']) !!}</td>
                    <td class="drop-row-weight">{!! Form::text('weight[]', $parameter->weight, ['class' => 'form-control drop-weight']) !!}</td>
                    <td class="drop-row-chance"></td>
                    <td class="text-right"><a href="#" class="btn btn-danger remove-drop-button">Remove</a></td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>

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
    {!! Form::select('rewardable_id[]', [1 => 'No reward given.'], null, ['class' => 'form-control none-select']) !!}

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
});
    
</script>
@endsection