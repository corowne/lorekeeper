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
    <div class="row ml-md-2">
      <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-bottom">
        @if(!$isClaims)
          <div class="col-12 col-md-2 font-weight-bold">Prompt</div>
        @endif
        <div class="col-6 {{ !$isClaims ? 'col-md-3' : 'col-md-4' }} font-weight-bold">Link</div>
        <div class="col-6 {{ !$isClaims ? 'col-md-5' : 'col-md-6' }} font-weight-bold">Submitted</div>
        <div class="col-12 col-md-1 font-weight-bold">Status</div>
      </div>

      @foreach($submissions as $submission)
        <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
          @if(!$isClaims)
            <div class="col-12 col-md-2">{!! $submission->prompt->displayName !!}</div>
          @endif
          <div class="col-6 {{ !$isClaims ? 'col-md-3' : 'col-md-4' }}">
            <span class="ubt-texthide"><a href="{{ $submission->url }}">{{ $submission->url }}</a></span>
          </div>
          <div class="col-6 {{ !$isClaims ? 'col-md-5' : 'col-md-6' }}">{!! pretty_date($submission->created_at) !!}</div>
          <div class="col-6 col-md-1 text-right">
            <span class="btn btn-{{ $submission->status == 'Pending' ? 'secondary' : ($submission->status == 'Approved' ? 'success' : 'danger') }} btn-sm py-0 px-1">{{ $submission->status }}</span>
          </div>
          <div class="col-6 col-md-1"><a href="{{ $submission->viewUrl }}" class="btn btn-primary btn-sm py-0 px-1">Details</a></div>
        </div>
      @endforeach
      </div>
    {!! $submissions->render() !!}
    <div class="text-center mt-4 small text-muted">{{ $submissions->total() }} result{{ $submissions->total() == 1 ? '' : 's' }} found.</div>
@else
    <p>No {{ $isClaims ? 'claims' : 'submissions' }} found.</p>
@endif

@endsection
