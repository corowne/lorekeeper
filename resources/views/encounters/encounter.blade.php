@extends('encounters.layout')

@section('title')
    {{ $area->name }} Encounter
@endsection

@section('content')
    {!! breadcrumbs(['Encounters' => 'encounter-areas', $area->name => 'encounter-areas/' . $area->id]) !!}
    <div>
        @if (Auth::check() && Auth::user()->hasPower('edit_data'))
        <a data-toggle="tooltip" title="[ADMIN] Edit Encounter"
            href="{{ url('admin/data/encounters/edit/') . '/' . $encounter->id }}" class="mb-2 float-right"><i
                class="fas fa-crown"></i></a>
    @endif
    <div class="row col-12 mb-2">
        <h1>{!! $encounter->name !!} in {{ $area->name }} </h1>
    </div>
        <div class="d-flex"
            style="position: relative; overflow: hidden; background:url({{ $area->imageUrl }}); height:500px;background-size: cover;">
            <!-- image -->
            @if ($encounter->has_image)
                <img src="{{ $encounter->imageUrl }}"
                    style="position: absolute; right: {{ isset($encounter->extras['position_right']) ? $encounter->extras['position_right'] : '80' }}%; bottom: {{ isset($encounter->extras['position_bottom']) ? $encounter->extras['position_bottom'] : '80' }}%; z-index: 2;">
            @endif
        </div>
        <div class="card bg-dark text-light rounded-0 text-center">
            <p>{!! $encounter->initial_prompt !!}</p>
            <h5> what do you do?</h5>
            <div class="row col-12 mb-2">
                <div class="col-md-10">
                    {!! Form::open(['url' => 'encounter-areas/' . $area->id . '/act']) !!}
                    {!! Form::hidden('area_id', $area->id) !!}
                    {!! Form::hidden('encounter_id', $encounter->id) !!}
                    {!! Form::select('action', $action_options, null, ['class' => 'form-control', 'placeholder' => 'Select Action']) !!}
                </div>
                <div class="col-md-2">
                    {!! Form::submit('Act!', ['class' => 'btn btn-primary']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
