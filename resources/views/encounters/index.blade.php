@extends('encounters.layout')

@section('title')
    Encounter Areas
@endsection

@section('content')
    {!! breadcrumbs(['Encounter Areas' => 'encounter-areas']) !!}

    <h1>Encounter Areas</h1>
    <p>Here is a list of areas that you can venture into. You will recieve a randomized encounter and options of what to do
        in it.</p>
    <p>You have limited energy to explore each day, so spend it wisely.</p>
    <p class="text-right"> You have <strong>{{ Auth::user()->settings->encounter_energy }}</strong> energy </p>
    @if (!count($areas))
        <div class="alert alert-info">No areas found. Check back later!</div>
    @else
        <div class="row shops-row">
            @foreach ($areas as $area)
                @include('encounters._area_entry')
            @endforeach
        </div>
    @endif
@endsection
