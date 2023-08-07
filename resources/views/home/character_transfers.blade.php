@extends('home.layout')

@section('home-title')
    Character Transfers
@endsection

@section('home-content')
    {!! breadcrumbs(['Character Transfers' => 'characters/transfers']) !!}

    <h1>
        Character Transfers
    </h1>

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link {{ set_active('characters/transfers/incoming*') }}" href="{{ url('characters/transfers/incoming') }}">Incoming</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ set_active('characters/transfers/outgoing*') }}" href="{{ url('characters/transfers/outgoing') }}">Outgoing</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ set_active('characters/transfers/completed*') }}" href="{{ url('characters/transfers/completed') }}">Completed</a>
        </li>
    </ul>

    {!! $transfers->render() !!}
    @foreach ($transfers as $transfer)
        @include('home._transfer', ['transfer' => $transfer])
    @endforeach
    {!! $transfers->render() !!}
@endsection
