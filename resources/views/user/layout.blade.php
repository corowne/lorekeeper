@extends('layouts.app')

@section('title') User ::@yield('profile-title')@endsection

@section('sidebar')
    @include('user._sidebar')
@endsection

@section('content')
    @yield('profile-content')
@endsection

@section('scripts')
@parent
@endsection