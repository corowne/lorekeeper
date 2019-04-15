@extends('user.layout')

@section('profile-title') {{ $user->name }}'s Bank @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Bank' => $user->url . '/bank']) !!}

<h1>
    {!! $user->displayName !!}'s Bank
</h1>


@endsection
