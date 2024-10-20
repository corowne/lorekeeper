<div>
    @if (Auth::check() && Auth::user()->hasPower('edit_data'))
        <a data-toggle="tooltip" title="[ADMIN] Edit Encounter" href="{{ url('admin/data/encounters/edit/') . '/' . $encounter->id }}" class="mb-2 float-right"><i class="fas fa-crown"></i></a>
    @endif
    <div class="row col-12 mb-2">
        <h1>{!! $encounter->name !!} in {{ $area->name }} </h1>
    </div>
    <div class="d-flex" style="position: relative; overflow: hidden; background:url({{ $area->imageUrl }}); height:500px;background-size: cover;">
        <!-- image -->
        @if ($encounter->has_image)
            <img src="{{ $encounter->imageUrl }}"
                style="position: absolute; right: {{ isset($encounter->extras['position_right']) ? $encounter->extras['position_right'] : '80' }}%; bottom: {{ isset($encounter->extras['position_bottom']) ? $encounter->extras['position_bottom'] : '80' }}%; z-index: 2;">
        @endif
    </div>
    <div class="card bg-dark text-light rounded-0 text-center">
        <p>{!! $encounter->initial_prompt !!}</p>
        <h5> what do you do?</h5>
        <hr>
        @foreach ($action_options as $option)
            {!! Form::open(['url' => 'encounter-areas/' . $area->id . '/act']) !!}
            {!! Form::hidden('area_id', $area->id) !!}
            {!! Form::hidden('encounter_id', $encounter->id) !!}
            {!! Form::hidden('action', $option->id) !!}
            <div class="form-group">
                {!! Form::submit($option->name, ['class' => 'btn btn-primary action-'. $option->id ]) !!}
            </div>
            {!! Form::close() !!}
        @endforeach
    </div>
</div>

