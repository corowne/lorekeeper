@extends('home.layout')

@section('home-title') Shop Index @endsection

@section('home-content')
{!! breadcrumbs(['Home' => 'home']) !!}

<h1>
    User Shops
</h1>

<div class="row shops-row">
    @foreach($shops as $shop)
        <div class="col-md-3 col-6 mb-3 text-center">
            <div class="shop-image">
                <a href="{{ $shop->url }}"><img src="{{ $shop->shopImageUrl }}" alt="{{ $shop->name }}" /></a>
            </div>
            <div class="shop-name mt-1">
                <a href="{{ $shop->url }}" class="h5 mb-0">{{ $shop->name }}</a>
            </div>
        </div>
    @endforeach
</div>

@endsection
