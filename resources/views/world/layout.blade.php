@extends('layouts.app')

@section('title')
    World ::
    @yield('world-title')
@endsection

@section('sidebar')
    @include('world._sidebar')
@endsection

@section('content')
    @yield('world-content')
@endsection

@section('scripts')
    @parent
@endsection
