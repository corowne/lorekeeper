@extends('layouts.app')

@section('title') {{ $sales->title }} @endsection

@section('content')
    {!! breadcrumbs(['Site Sales' => 'sales', $sales->title => $sales->url]) !!}
    @include('sales._sales', ['sales' => $sales])
@endsection
