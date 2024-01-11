@extends('layouts.app')

@section('title') 
    Encounters :: 
    @yield('encounters-title')
@endsection

@section('sidebar')
    @include('encounters._sidebar')
@endsection

@section('scripts')
@parent
@endsection