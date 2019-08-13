@extends('layouts.app')

@section('title') 
    Character :: 
    @yield('profile-title')
@endsection

@section('sidebar')
    @include('character.'.($isMyo ? 'myo.' : '').'_sidebar')
@endsection

@section('content')
    @yield('profile-content')
@endsection

@section('scripts')
@parent
@endsection