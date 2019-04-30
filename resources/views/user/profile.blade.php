@extends('user.layout')

@section('profile-title') {{ $user->name }}'s Profile @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url]) !!}

<h1>
    {!! $user->displayName !!}

    @if($user->settings->is_fto)
        <span class="badge badge-success float-right" data-toggle="tooltip" title="This user has not owned any characters from this world before.">FTO</span>
    @endif
</h1>
<div class="mb-1">
    <div class="row">
        <div class="col-md-2 col-4"><h5>Alias</h5></div>
        <div class="col-md-10 col-8">{!! $user->displayAlias !!}</div>
    </div>
    <div class="row">
        <div class="col-md-2 col-4"><h5>Rank</h5></div>
        <div class="col-md-10 col-8">{!! $user->rank->displayName !!} {!! add_help($user->rank->parsed_description) !!}</div>
    </div>
    <div class="row">
        <div class="col-md-2 col-4"><h5>Joined</h5></div>
        <div class="col-md-10 col-8">{{ format_date($user->created_at) }} ({{ $user->created_at->diffForHumans() }})</div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">

        Custom user profile content goes here.

    </div>
</div>

<div class="card-deck mb-4 profile-assets">
    <div class="card profile-currencies profile-assets-card">
        <div class="card-body text-center">
            <h5 class="card-title">Bank</h5>
            <div class="profile-assets-content">
                @foreach($user->getCurrencies(true) as $currency)
                    <div>{!! $currency->display($currency->quantity) !!}</div>
                @endforeach
            </div>
            <div class="text-right"><a href="{{ $user->url.'/bank' }}">View all...</a></div>
        </div>
    </div>
    <div class="card profile-inventory profile-assets-card">
        <div class="card-body text-center">
            <h5 class="card-title">Inventory</h5>
            <div class="profile-assets-content">
                @if(count($items))
                    <div class="row">
                        @foreach($items as $item)
                            <div class="col-md-3 col-6 profile-inventory-item">
                                <img src="{{ $item->imageUrl }}" data-toggle="tooltip" title="{{ $item->name }}" />
                            </div>
                        @endforeach
                    </div>
                @else 
                    <div>No items owned.</div>
                @endif
            </div>
            <div class="text-right"><a href="{{ $user->url.'/inventory' }}">View all...</a></div>
        </div>
    </div>
</div>

<h2>Characters</h2>
<div class="row">
    @for($i = 0; $i < 8; $i++)
        <div class="col-md-3 col-6 mb-3">
            <div class="card">
                <img class="card-img" src="https://via.placeholder.com/150" />
            </div>
        </div>
    @endfor
</div>

@endsection
