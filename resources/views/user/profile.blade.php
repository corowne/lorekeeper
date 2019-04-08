@extends('user.layout')

@section('profile-title') {{ $user->name }}'s Profile @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name . '\'s Profile' => $user->url]) !!}

<h1>
    {!! $user->displayName !!}

    @if($user->settings->is_fto)
        <span class="badge badge-success float-right">FTO</span>
    @endif
</h1>
<div class="mb-1">
    <div class="row">
        <div class="col-md-2 col-4"><h5>Alias</h5></div>
        <div class="col-md-10 col-8">{!! $user->displayAlias !!}</div>
    </div>
    <div class="row">
        <div class="col-md-2 col-4"><h5>Rank</h5></div>
        <div class="col-md-10 col-8">{!! $user->rank->displayName !!} {!! add_help($user->rank->description) !!}</div>
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

<div class="card-deck mb-4">
    <div class="card">
        <div class="card-body text-center">
            <h5 class="card-title">Bank</h5>
            <div>500 G</div>
            <div>10 Coin</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body text-center">
            <h5 class="card-title">Items</h5>
            <div>Last 8 items</div>
            <div>View all...</div>
        </div>
    </div>
</div>

<h2>Characters (first 8 by sort order)</h2>
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
