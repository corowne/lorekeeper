@extends('home.layout')

@section('home-title') Prompt Submissions @endsection

@section('home-content')
{!! breadcrumbs(['Prompt Submissions' => 'submissions']) !!}

<h1>
    Prompt Submissions
</h1>

<div class="text-right">
    <a href="{{ url('submissions/new') }}" class="btn btn-success">New Submission</a>
</div>

<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ !Request::get('type') || Request::get('type') == 'pending' ? 'active' : '' }}" href="{{ url('submissions') }}">Pending</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::get('type') == 'approved' ? 'active' : '' }}" href="{{ url('submissions') . '?type=approved' }}">Approved</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::get('type') == 'rejected' ? 'active' : '' }}" href="{{ url('submissions') . '?type=rejected' }}">Rejected</a>
    </li>
</ul>

@if(count($submissions))
    {!! $submissions->render() !!}
    <table class="table table-sm">
        <thead>
            <tr>
                <th width="30%">Prompt</th>
                <th width="30%">Link</th>
                <th width="20%">Submitted</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($submissions as $submission)
                @include('home._submission', ['submission' => $submission])
            @endforeach
        </tbody>
    </table>
    {!! $submissions->render() !!}
@else 
    <p>No submissions found.</p>
@endif

@endsection
