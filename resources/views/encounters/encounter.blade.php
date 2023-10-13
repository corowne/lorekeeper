@extends('encounters.layout')

@section('title')
    {{ $area->name }} Encounter
@endsection

@section('content')
    {!! breadcrumbs(['Encounters' => 'encounter-areas', $area->name => 'encounter-areas/' . $area->id]) !!}

    <h1>Encounter in {{ $area->name }} </h1>
    <div class="text-center">
        <h3>{!! $encounter->name !!}</h3>
        <div class="d-flex align-items-end justify-content-center"
            style="background:url({{ $area->imageUrl }}); height:500px;background-size: cover;">
            <!-- image -->
            @if ($encounter->has_image)
                <img src="{{ $encounter->imageUrl }}"
                    style="position: absolute; right: {{ isset($encounter->extras['position_right']) ? $encounter->extras['position_right'] : '80' }}%; bottom: {{ isset($encounter->extras['position_bottom']) ? $encounter->extras['position_bottom'] : '80' }}%; z-index: 2;">
            @endif
        </div>
        <p>{!! $encounter->initial_prompt !!}</p>
        <h5> what do you do?</h5>
        {!! Form::open(['url' => 'encounter-areas/' . $area->id . '/act']) !!}
        {!! Form::hidden('area_id', $area->id) !!}
        {!! Form::hidden('encounter_id', $encounter->id) !!}
        {!! Form::select('action', $action_options, null, ['class' => 'form-control', 'placeholder' => 'Select Action']) !!}
        {!! Form::submit('Act!', ['class' => 'btn btn-primary']) !!}
        {!! Form::close() !!}
    </div>
@endsection
