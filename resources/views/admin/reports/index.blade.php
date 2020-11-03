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
<table>
    <thead>
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>User</th>
                    <th width="20%">Link / Title</th>
                    <th width="20%">Submitted</th>
                    <th>Status</th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports as $report)
                    <tr>
                        <td>{!! $report->user->displayName !!}</td>
                        <td class="text-break"><a href="{{ $report->url }}">{{ $report->url }}</a></td>
                        <td>{!! format_date($report->created_at) !!}</td>
                        <td>
                            <span class="badge badge-{{ $report->status == 'Pending' ? 'secondary' : ($report->status == 'Closed' ? 'success' : 'danger') }}">{{ $report->status }}</span>
                        </td>
                        @if($report->status !== 'Pending')<td>
                            Assigned to: {!! $report->staff->displayName !!}</td> @endif
                        <td>
                            @if($report->is_br == 1)<span class="badge badge-danger">Bug Report</span></td>@endif
                        <td>
                            @if($report->is_br == 1) {{ $report->error_type }} error @endif
                        </td>
                        <td class="text-right"><a href="{{ $report->adminUrl }}" class="btn btn-primary btn-sm">Details</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </thead>
</table>
{!! $reports->render() !!}


@endsection