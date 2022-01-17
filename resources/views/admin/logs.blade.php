@extends('admin.layout')

@section('admin-title') Logs @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Logs' => 'admin/logs']) !!}

<h1>Admin Logs</h1>

{!! $logs->render() !!}
<table class="table table-sm">
    <thead>
        <th>Staff</th>
        <th>Action</th>
        <th>Action Details</th>
        <th>Date</th>
    </thead>
    <tbody>
        @foreach($logs as $log)
        <tr>
            <td>{!! $log->user->displayName !!}</td>
            <td>{!! $log->action !!}</td>
            <td>{!! $log->action_details !!}</td>
            <td>{!! format_date($log->created_at) !!} ({!! pretty_date($log->created_at) !!})</td>
        </tr>
        @endforeach
    </tbody>
</table>
{!! $logs->render() !!}

@endsection
