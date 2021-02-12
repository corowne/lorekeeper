@extends('world.layout')

@section('title') Home @endsection

@section('content')
{!! breadcrumbs(['Encyclopedia' => 'world', 'Levels' => 'levels']) !!}

<h1>World</h1>

    <div class="card-body text-center">
        <img src="{{ asset('images/account.png') }}" />
        <h5 class="card-title">Levels</h5>
    </div>
    <ul class="list-group list-group-flush">
        <li class="list-group-item"><a href="{{ url('world/levels/user') }}">User Levels</a></li>
        <li class="list-group-item"><a href="{{ url('world/levels/character') }}">Character Levels</a></li>
        <li class="list-group-item"><a href="{{ url('world/stats') }}">Stats</a></li>
    </ul>
    
@endsection
