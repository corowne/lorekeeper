@extends('admin.layout')

@section('admin-title')
    Encounter Areas
@endsection

@section('admin-content')
    {!! breadcrumbs([
        'Admin Panel' => 'admin',
        'Encounter Areas' => 'admin/data/encounters/areas/',
        ($area->id ? 'Edit' : 'Create') . ' Area' => $area->id
            ? 'admin/data/encounters/areas/edit/' . $area->id
            : 'admin/data/encounters/areas/create',
    ]) !!}

    <h1>
        {{ $area->id ? 'Edit' : 'Create' }} Area
        @if ($area->id)
            <a href="#" class="btn btn-danger float-right delete-area-button">Delete Area</a>
        @endif
    </h1>

    {!! Form::open([
        'url' => $area->id ? 'admin/data/encounters/areas/edit/' . $area->id : 'admin/data/encounters/areas/create',
        'files' => true,
    ]) !!}

    <h3>Basic Information</h3>

    <div class="form-group">
        {!! Form::label('Name') !!}
        {!! Form::text('name', $area->name, ['class' => 'form-control']) !!}
    </div>

    <div class="row">
        @if ($area->has_image)
            <div class="col-md-2">
                <div class="form-group">
                    <img src="{{ $area->imageUrl }}" class="img-fluid mr-2 mb-2" style="height: 10em;" />
                    <br>
                </div>
            </div>
        @endif
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('World Page Image (Optional)') !!} {!! add_help('This image is used on the world information pages and side widget.') !!}
                <div>{!! Form::file('image') !!}</div>
                <div class="text-muted">Recommended size: 100px x 100px</div>
                @if ($area->has_image)
                    <div class="form-check">
                        {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
                        {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('Description (Optional)') !!}
        {!! Form::textarea('description', $area->description, ['class' => 'form-control wysiwyg']) !!}
    </div>

    <div class="form-group">
        {!! Form::checkbox('is_active', 1, $area->id ? $area->is_active : 1, [
            'class' => 'form-check-input',
            'data-toggle' => 'toggle',
        ]) !!}
        {!! Form::label('is_active', 'Is Active', ['class' => 'form-check-label ml-3']) !!} {!! add_help(
            'Areas that are not active will be hidden from the area list. They also cannot be automatically set as the next active area.',
        ) !!}
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('start_at', 'Start Time') !!} {!! add_help('Areas won\'t rotate in until this time is reached.') !!}
                {!! Form::text('start_at', $area->start_at, ['class' => 'form-control datepicker']) !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('end_at', 'End Time') !!} {!! add_help('A area won\'t be able to be automatically activated after this window of time ends.') !!}
                {!! Form::text('end_at', $area->end_at, ['class' => 'form-control datepicker']) !!}
            </div>
        </div>
    </div>


    <h3>Table</h3>

    <p>These are the potential encounters from rolling on this area.@if (!$area->id)
            You can test rolling after the area is created.
        @endif
    </p>
    <div class="text-right mb-3">
        <a href="#" class="btn btn-info" id="addEncounter">Add Encounter</a>
    </div>
    <table class="table table-sm" id="encounterArea">
        <thead>
            <tr>
                <th width="40%">Encounter</th>
                <th width="30%">Weight {!! add_help(
                    'A higher weight means a reward is more likely to be rolled. Weights have to be integers above 0 (round positive number, no decimals) and do not have to add up to be a particular number.',
                ) !!}</th>
                <th width="20%">Chance</th>
                <th width="10%"></th>
            </tr>
        </thead>
        <tbody id="encounterAreaBody">
            @if ($area->id)
                @foreach ($area->encounters as $encounter)
                    <tr class="encounter-row">
                        <td class="encounter-row-select">
                            {!! Form::select('encounter_id[]', $encounters, $encounter->encounter_id, [
                                'class' => 'form-control encounter-select selectize',
                                'placeholder' => 'Select Encounter',
                            ]) !!}
                        </td>
                        <td class="encounter-row-weight">{!! Form::text('weight[]', $encounter->weight, ['class' => 'form-control encounter-weight']) !!}</td>
                        <td class="encounter-row-chance"></td>
                        <td class="text-right"><a href="#" class="btn btn-danger remove-encounter-button">Remove</a>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <div class="text-right">
        {!! Form::submit($area->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}

    <div id="encounterRowData" class="hide">
        <table class="table table-sm">
            <tbody id="encounterRow">
                <tr class="encounter-row">
                    <td class="encounter-row-select">{!! Form::select('encounter_id[]', $encounters, null, [
                        'class' => 'form-control encounter-select',
                        'placeholder' => 'Select Encounter',
                    ]) !!}</td>
                    <td class="encounter-row-weight">{!! Form::text('weight[]', 1, ['class' => 'form-control encounter-weight']) !!}</td>
                    <td class="encounter-row-chance"></td>
                    <td class="text-right"><a href="#" class="btn btn-danger remove-encounter-button">Remove</a></td>
                </tr>
            </tbody>
        </table>
    </div>

    @if ($area->id)
        <h3>Test Roll</h3>
        <p>If you have made any modifications to the area contents above, be sure to save it (click the Edit button) before
            testing.</p>
        <p>Please note that due to the nature of probability, as long as there is a chance, there will always be the
            possibility of rolling improbably good or bad results. <i>This is not indicative of the code being buggy or poor
                game balance.</i> Be cautious when adjusting values based on a small sample size, including but not limited
            to test rolls and a small amount of user reports.</p>
        <div class="form-group">
            {!! Form::label('quantity', 'Number of Rolls') !!}
            {!! Form::text('quantity', 1, ['class' => 'form-control', 'id' => 'rollQuantity']) !!}
        </div>
        <div class="text-right">
            <a href="#" class="btn btn-primary" id="testRoll">Test Roll</a>
        </div>
    @endif

    @if ($area->id)
        <h3>Preview</h3>
        <div class="card mb-3">
            <div class="card-body">
                put preview here (user view)
            </div>
        </div>
    @endif

@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.delete-area-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/data/encounters/areas/delete') }}/{{ $area->id }}",
                    'Delete Area');
            });
            var $encounterArea = $('#encounterAreaBody');
            var $encounterRow = $('#encounterRow').find('.encounter-row');
            var $encounterSelect = $('#encounterRowData').find('.encounter-select');
            $(".datepicker").datetimepicker({
                dateFormat: "yy-mm-dd",
                timeFormat: 'HH:mm:ss',
            });
            refreshChances();
            $('#encounterAreaBody .selectize').selectize();
            attachRemoveListener($('#encounterAreaBody .remove-encounter-button'));
            $('#testRoll').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/data/encounters/areas/roll') }}/{{ $area->id }}?quantity=" + $(
                    '#rollQuantity').val(), 'Rolling Area');
            });
            $('#addEncounter').on('click', function(e) {
                e.preventDefault();
                var $clone = $encounterRow.clone();
                $encounterArea.append($clone);
                attachRemoveListener($clone.find('.remove-encounter-button'));
                attachWeightListener($clone.find('.encounter-weight'));
                refreshChances();
            });
            $('.reward-type').on('change', function(e) {
                var val = $(this).val();
                var $cell = $(this).parent().find('.encounter-row-select');
                var $clone = null;
                if (val == 'Encounter') $clone = $encounterSelect.clone();
                $cell.html('');
                $cell.append($clone);
            });

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
                $('#encounterAreaBody .encounter-weight').each(function(index) {
                    var current = parseInt($(this).val());
                    total += current;
                    weights.push(current);
                });
                $('#encounterAreaBody .encounter-row-chance').each(function(index) {
                    var current = (weights[index] / total) * 100;
                    $(this).html(current.toString() + '%');
                });
            }
        });
    </script>
@endsection
