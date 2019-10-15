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
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
</div>

{!! $users->render() !!}
<table class="users-table table table-sm table-responsive-xs">
    <thead>
        <tr>
            <th>Username</th>
            <th>Alias</th>
            <th>Rank</th>
            <th>Joined</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
            <tr>
                <td>{!! $user->displayName !!}</td>
                <td>{!! $user->displayAlias !!}</td>
                <td>{!! $user->rank->displayName !!}</td>
                <td>{{ format_date($user->created_at) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
{!! $users->render() !!}

<div class="text-center mt-4 small text-muted">{{ $users->total() }} result{{ $users->total() == 1 ? '' : 's' }} found.</div>

@endsection
