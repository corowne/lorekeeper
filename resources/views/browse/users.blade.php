@extends('layouts.app')

@section('title') Users @endsection

@section('content')
{!! breadcrumbs(['Users' => 'users']) !!}
<h1>
    User Index
    @if($blacklistLink)
        <a href="{{ url('blacklist') }}" class="btn btn-dark float-right">Blacklist</a>
    @endif
</h1>

<div>
    {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::text('name', Request::get('name'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mr-3 mb-3">
            {!! Form::select('rank_id', $ranks, Request::get('rank_id'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mr-3 mb-3">
            {!! Form::select('sort', [
                'alpha'          => 'Sort Alphabetically (A-Z)',
                'alpha-reverse'  => 'Sort Alphabetically (Z-A)',
                'alias'          => 'Sort by Alias (A-Z)',
                'alias-reverse'  => 'Sort by Alias (Z-A)',
                'rank'           => 'Sort by Rank (Default)',
                'newest'         => 'Newest First',
                'oldest'         => 'Oldest First'    
            ], Request::get('sort') ? : 'category', ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
</div>
{!! $users->render() !!}
  <div class="row ml-md-2">
    <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
      <div class="col-12 col-md-4 font-weight-bold">Username</div>
      <div class="col-4 col-md-3 font-weight-bold">Primary Alias</div>
      <div class="col-4 col-md-2 font-weight-bold">Rank</div>
      <div class="col-4 col-md-3 font-weight-bold">Joined</div>
    </div>
    @foreach($users as $user)
    <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
      <div class="col-12 col-md-4 ">{!! $user->displayName !!}</div>
      <div class="col-4 col-md-3">{!! $user->displayAlias !!}</div>
      <div class="col-4 col-md-2">{!! $user->rank->displayName !!}</div>
      <div class="col-4 col-md-3">{!! pretty_date($user->created_at, false) !!}</div>
    </div>
    @endforeach
  </div>
{!! $users->render() !!}

<div class="text-center mt-4 small text-muted">{{ $users->total() }} result{{ $users->total() == 1 ? '' : 's' }} found.</div>

@endsection
