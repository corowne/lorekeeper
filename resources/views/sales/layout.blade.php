@extends('layouts.app')

@section('title')
    Site Sales :: @yield('sales-title')
@endsection

@section('sidebar')
    @include('sales._sidebar')
@endsection

@section('content')
    @yield('sales-content')
@endsection
