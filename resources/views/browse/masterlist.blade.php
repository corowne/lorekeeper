@extends('layouts.app')

@section('title') Character Masterlist @endsection

@section('content')
{!! breadcrumbs(['Character Masterlist' => 'masterlist']) !!}
<h1>Character Masterlist</h1>

@include('browse._masterlist_content', ['characters' => $characters])

@endsection

@section('scripts')
@include('browse._masterlist_js')
@endsection