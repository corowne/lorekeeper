@extends('admin.layout')

@section('admin-title') Report Queue @endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Report Queue' => 'admin/reports/pending']) !!}

<h1>
    Report Queue
</h1>

<ul class="nav nav-tabs mb-3">
  <li class="nav-item">
    <a class="nav-link {{ set_active('admin/reports/pending*') }} {{ set_active('admin/reports') }}" href="{{ url('admin/reports/pending') }}">Pending</a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ set_active('admin/reports/assigned*') }} {{ set_active('admin/reports') }}" href="{{ url('admin/reports/assigned') }}">Assigned</a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ set_active('admin/reports/closed*') }} {{ set_active('admin/reports') }}" href="{{ url('admin/reports/closed') }}">Closed</a>
  </li>
</ul>

{!! $reports->render() !!}

<div class="row ml-md-2">
  <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-bottom">
    <div class="col-6 col-md-3 font-weight-bold">User</div>
    <div class="col-6 col-md-4 font-weight-bold">Url/Title</div>
    <div class="col-6 col-md-3 font-weight-bold">Submitted</div>
    <div class="col-6 col-md-1 font-weight-bold">Status</div>
  </div>

  @foreach($reports as $report)
    <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
      <div class="col-6 col-md-3">{!! $report->user->displayName !!}</div>
      <div class="col-6 col-md-4">
        <span class="ubt-texthide">@if(!$report->is_br)<a href="{{ $report->url }}">@endif {{ $report->url }} @if(!$report->is_br)</a>@endif</span>
      </div>
      <div class="col-6 col-md-3">{!! pretty_date($report->created_at) !!}</div>
      <div class="col-3 col-md-1">
        <span class="badge badge-{{ $report->status == 'Pending' ? 'secondary' : ($report->status == 'Closed' ? 'success' : 'danger') }}">{{ $report->status }}</span>
      </div>
      <div class="col-3 col-md-1"><a href="{{ $report->adminUrl }}" class="btn btn-primary btn-sm py-0 px-1">Details</a></div>
    </div>
  @endforeach
</div>

{!! $reports->render() !!}
<div class="text-center mt-4 small text-muted">{{ $reports->total() }} result{{ $reports->total() == 1 ? '' : 's' }} found.</div>

@endsection