@extends('home.user_shops.layout')

@section('home.user_shops-title')
    Shop Index
@endsection

@section('home.user_shops-content')
    {!! breadcrumbs(['User Shops' => 'user-shops/shop-index']) !!}

    <h1>
        User Shops
    </h1>
    <p>These are user-owned shops that sell items. Not to be confused with official, admin-made shops.</p>
    <div class="text-right mb-3">
        <a class="btn btn-primary" href="{{ url('user-shops/item-search') }}"><i class="fas fa-search mr-1"></i>Search by
            Items</a>
    </div>

    @if (Auth::user()->isStaff)
        <div class="alert alert-info text-center">
            You can see hidden shops and shops from banned users because you are staff.
        </div>
    @endif

    <div>
        {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::text('name', Request::get('name'), [
                'class' => 'form-control',
                'placeholder' => 'Search by Shop Name',
            ]) !!}
        </div>
        <div class="form-group mr-3 mb-3">
            {!! Form::select(
                'sort',
                [
                    'alpha' => 'Sort Alphabetically (A-Z)',
                    'alpha-reverse' => 'Sort Alphabetically (Z-A)',
                    'newest' => 'Newest First',
                    'oldest' => 'Oldest First',
                    'update' => 'Sort Last Updated (New-Old)',
                    'update-reverse' => 'Sort Last Updated (Old-New)',
                ],
                Request::get('sort') ?: 'category',
                ['class' => 'form-control'],
            ) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}
    </div>

    {!! $shops->render() !!}
    <div class="row shops-row">
        @foreach ($shops as $shop)
            <div class="col-md-3 col-6 mb-3 text-center">
                @if ($shop->has_image)
                    <div class="shop-image container">
                        <a href="{{ $shop->url }}">
                            <img src="{{ $shop->shopImageUrl }}"
                                style="max-width: 200px !important; max-height: 200px !important;"
                                alt="{{ $shop->name }}" />
                        </a>
                    </div>
                @endif
                <div class="shop-name mt-1">
                    <h5 class="mb-0">{!! $shop->displayName !!}</h5>
                    Owned by <a href="{{ $shop->user->url }}">{!! $shop->user->displayName !!}</a>
                </div>
                <div class="shop-name mt-1">
                   <strong>Stock</strong>: {{ $shop->visibleStock->count() }}
                </div>
            </div>
        @endforeach
    </div>
    {!! $shops->render() !!}


    <div class="text-center mt-4 small text-muted">{{ $shops->total() }} result{{ $shops->total() == 1 ? '' : 's' }} found.
    </div>

    <div class="text-right mb-4">
        <a href="{{ url('user-shops/history') }}">View purchase logs...</a>
    </div>
@endsection
