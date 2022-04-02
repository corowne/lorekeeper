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
        <a class="nav-link {{ set_active('admin/reports/assigned-to-me*') }} {{ set_active('admin/reports') }}" href="{{ url('admin/reports/assigned-to-me') }}">Assigned To Me</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ set_active('admin/reports/assigned') }} {{ set_active('admin/reports') }}" href="{{ url('admin/reports/assigned') }}">Assigned</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ set_active('admin/reports/closed*') }} {{ set_active('admin/reports') }}" href="{{ url('admin/reports/closed') }}">Closed</a>
    </li>
</ul>

{!! $reports->render() !!}
<div class="mb-4 logs-table">
    <div class="logs-table-header">
        <div class="row">
            <div class="col-6 col-md-3"><div class="logs-table-cell">User</div></div>
            <div class="col-6 col-md-4"><div class="logs-table-cell">Url/Title</div></div>
            <div class="col-6 col-md-2"><div class="logs-table-cell">Submitted</div></div>
            <div class="col-6 col-md-2"><div class="logs-table-cell">Status</div></div>
        </div>
    </div>
    <div class="logs-table-body">
        @foreach($reports as $report)
            <div class="logs-table-row">
                <div class="row flex-wrap">
                    <div class="col-6 col-md-3"><div class="logs-table-cell">{!! $report->user->displayName !!}</div></div>
                    <div class="col-6 col-md-4">
                        <div class="logs-table-cell">
                            <span class="ubt-texthide">@if(!$report->is_br)<a href="{{ $report->url }}">@endif {{ $report->url }} @if(!$report->is_br)</a>@endif</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-2"><div class="logs-table-cell">{!! pretty_date($report->created_at) !!}</div></div>
                    <div class="col-3 col-md-2">
                        <div class="logs-table-cell">
                            <span class="badge badge-{{ $report->status == 'Pending' ? 'secondary' : ($report->status == 'Closed' ? 'success' : 'danger') }}">{{ $report->status }}</span>{!! $report->status == 'Assigned' ? ' (to '.$report->staff->displayName.')' : '' !!}
                        </div>
                    </div>
                    <div class="col-3 col-md-1"><div class="logs-table-cell"><a href="{{ $report->adminUrl }}" class="btn btn-primary btn-sm py-0 px-1">Details</a></div></div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{!! $reports->render() !!}
<div class="text-center mt-4 small text-muted">{{ $reports->total() }} result{{ $reports->total() == 1 ? '' : 's' }} found.</div>

@endsection