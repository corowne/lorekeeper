@extends('user.layout')

@section('profile-title') {{ $user->name }}'s User Shops @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Shops' => $user->url . '/shops']) !!}

<h1>
    {!! $user->displayName !!}'s User Shops
</h1>


@if($shops->count())
    <div class="row shops-row">
        @foreach($shops as $shop)
            <div class="col-md-3 col-6 mb-3 text-center">
                @if ($shop->has_image)
                    <div class="shop-image">
                        <a href="{{ $shop->url }}"><img src="{{ $shop->shopImageUrl }}" alt="{{ $shop->name }}" /></a>
                    </div>
                @endif
                <div class="shop-name mt-1">
                    <a href="{{ $shop->url }}" class="h5 mb-0">{{ $shop->name }}</a>
                </div>
            </div>
        @endforeach
    </div>
@else
    <p>No shops found.</p>
@endif

@endsection