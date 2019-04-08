@extends('layouts.app')

@section('title') User List @endsection

@section('content')
<h1>User List</h1>

<div>
    {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mx-sm-3 mb-3">
            {!! Form::text('name', Request::get('name'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mx-sm-3 mb-3">
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
                <td>{{ $user->name }}</td>
                <td>{{ $user->alias }}</td>
                <td>Rank</td>
                <td>{{ $user->created_at }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
{!! $users->render() !!}

@endsection
