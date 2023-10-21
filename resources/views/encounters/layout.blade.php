@extends('layouts.app')

@section('title') 
    Encounters :: 
    @yield('encounters-title')
@endsection

@section('sidebar')
    @include('encounters._sidebar')
@endsection

@section('content')
    @yield('encounters-content')
@endsection

@section('scripts')
@parent
@endsection