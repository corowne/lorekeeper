@extends('encounters.layout')

@section('title')
    Encounter Areas
@endsection

@section('content')
    {!! breadcrumbs(['Encounter Areas' => 'encounter-areas']) !!}

    <h1>Encounter Areas</h1>
    <p>explore around</p>
    <p class="text-right"> you have <strong>{{ Auth::user()->settings->encounter_energy }}</strong> energy </p>
    @foreach ($areas as $area)
        @include('encounters._area_entry')
    @endforeach
@endsection
