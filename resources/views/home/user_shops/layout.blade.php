@extends('layouts.app')

@section('title') 
    Shops :: 
    @yield('home.user_shops-title')
@endsection

@section('sidebar')
    @include('home.user_shops._sidebar')
@endsection

@section('content')
    @yield('home.user_shops-content')
@endsection

@section('scripts')
@parent
@endsection