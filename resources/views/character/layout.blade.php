@extends('layouts.app')

@section('title') 
    Character :: 
    @yield('profile-title')
@endsection

@section('sidebar')
    @include('character._sidebar')
@endsection

@section('content')
    @yield('profile-content')
@endsection

@section('scripts')
@parent
@endsection