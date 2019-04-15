@extends('user.layout')

@section('profile-title') {{ $user->name }}'s Inventory @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Inventory' => $user->url . '/inventory']) !!}

<h1>
    {!! $user->displayName !!}'s Inventory
</h1>


@endsection
