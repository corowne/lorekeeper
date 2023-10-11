@extends('encounters.layout')

@section('title')
    {{ $area->name }} Encounter
@endsection

@section('content')
    {!! breadcrumbs(['Encounters' => 'encounter-areas', $area->name => 'encounter-areas/' . $area->id]) !!}

    <h1>Encounter in {{ $area->name }} </h1>
    <div class="text-center">
        <h1>{{ $encounter->name }}</h1>
        @if ($encounter->has_image)
            <img src="{{ $encounter->imageUrl }}" />
        @endif
        {!! $encounter->initial_prompt !!}
        <h5> what do you do?</h5>
        {!! Form::open(['url' => 'encounter-areas/' . $area->id . '/act']) !!}
        {!! Form::hidden('area_id', $area->id) !!}
        {!! Form::hidden('encounter_id', $encounter->id) !!}
        {!! Form::select('action', $action_options, null, ['class' => 'form-control', 'placeholder' => 'Select Action']) !!}
        {!! Form::submit('Act!', ['class' => 'btn btn-primary']) !!}
        {!! Form::close() !!}
    </div>
@endsection
