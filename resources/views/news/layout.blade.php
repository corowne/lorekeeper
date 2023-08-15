@extends('layouts.app')

@section('title')
    Site News :: @yield('news-title')
@endsection

@section('sidebar')
    @include('news._sidebar')
@endsection

@section('content')
    @yield('news-content')
@endsection
