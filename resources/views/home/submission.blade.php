@extends('user.layout')

@section('profile-title') {{ $submission->prompt_id ? 'Submission' : 'Claim' }} (#{{ $submission->id }}) @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, $submission->prompt_id ? 'Submission' : 'Claim (#' . $submission->id . ')' => $submission->viewUrl]) !!}

@include('home._submission_content', ['submission' => $submission])

@endsection
