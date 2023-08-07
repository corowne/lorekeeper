@extends('layouts.app')

@section('title')
    MYO Slot Masterlist
@endsection

@section('sidebar')
    @include('browse._sidebar')
@endsection

@section('content')
    {!! breadcrumbs(['MYO Slot Masterlist' => 'myos']) !!}
    <h1>MYO Slot Masterlist</h1>

    @include('browse._masterlist_content', ['characters' => $slots])
@endsection

@section('scripts')
    @include('browse._masterlist_js')
@endsection
