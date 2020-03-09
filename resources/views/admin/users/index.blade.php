@extends('admin.layout')

@section('admin-title') User Index @stop

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'User Index' => 'admin/users']) !!}

<h1>User Index</h1>

<p>Click on a user's name to view/edit their information.</p>

<div>
    {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-sm-3 mb-3">
            {!! Form::text('name', Request::get('name'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mr-sm-3 mb-3">
            {!! Form::select('rank_id', $ranks, Request::get('rank_id'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
</div>

{!! $users->render() !!}
<table class="table table-sm">
    <thead>
        <th>Username</th>
        <th>Alias</th>
        <th>Rank</th>
        <th>Joined</th>
    </thead>
    <tbody>
        @foreach($users as $user)
            <tr>
                <td><a href="{{ $user->adminUrl }}">{!! $user->is_banned ? '<strike>' : '' !!}{{ $user->name }}{!! $user->is_banned ? '</strike>' : '' !!}</a></td>
                <td>{!! $user->displayAlias !!}</td>
                <td>{!! $user->rank->displayName !!}</td>
                <td>{!! format_date($user->created_at) !!}</td>
            </tr>
        @endforeach
    </tbody>
</table>
{!! $users->render() !!}

<div class="text-center mt-4 small text-muted">{{ $count }} user{{ $count == 1 ? '' : 's' }} found.</div>

@endsection