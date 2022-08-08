@extends('layouts.app')

@section('title')
    Account ::
    @yield('account-title')
@endsection

@section('sidebar')
    @include('account._sidebar')
@endsection

@section('content')
    @yield('account-content')
@endsection
