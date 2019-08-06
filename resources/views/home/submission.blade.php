@extends('user.layout')

@section('profile-title') Submission (#{{ $submission->id }}) @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Submission (#' . $submission->id . ')' => $submission->viewUrl]) !!}

@include('home._submission_content', ['submission' => $submission])

@endsection