@extends('layouts.app')

@section('title') {{ $sublist->name }} Masterlist @endsection

@section('content')
{!! breadcrumbs([$sublist->name.' Masterlist' => $sublist->key ]) !!}
<h1>{{ $sublist->name }} Masterlist</h1>

@include('browse._masterlist_content', ['characters' => $characters])

@endsection

@section('scripts')
@include('browse._masterlist_js')
@endsection