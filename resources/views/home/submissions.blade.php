@extends('home.layout')

@section('home-title') {{ $isClaims ? 'Claims' : 'Prompt Submissions' }} @endsection

@section('home-content')
@if($isClaims)
    {!! breadcrumbs(['Claims' => 'claims']) !!}
@else 
    {!! breadcrumbs(['Prompt Submissions' => 'submissions']) !!}
@endif

<h1>
    {{ $isClaims ? 'Claims' : 'Prompt Submissions' }}
</h1>

<div class="text-right">
    @if(!$isClaims)
        <a href="{{ url('submissions/new') }}" class="btn btn-success">New Submission</a>
    @else 
        <a href="{{ url('claims/new') }}" class="btn btn-success">New Claim</a>
    @endif
</div>

<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ !Request::get('type') || Request::get('type') == 'pending' ? 'active' : '' }}" href="{{ url($isClaims ? 'claims' : 'submissions') }}">Pending</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::get('type') == 'approved' ? 'active' : '' }}" href="{{ url($isClaims ? 'claims' : 'submissions') . '?type=approved' }}">Approved</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ Request::get('type') == 'rejected' ? 'active' : '' }}" href="{{ url($isClaims ? 'claims' : 'submissions') . '?type=rejected' }}">Rejected</a>
    </li>
</ul>

@if(count($submissions))
    {!! $submissions->render() !!}
    <table class="table table-sm">
        <thead>
            <tr>
                @if(!$isClaims)
                    <th width="30%">Prompt</th>
                @endif
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
    <p>No {{ $isClaims ? 'claims' : 'submissions' }} found.</p>
@endif

@endsection
