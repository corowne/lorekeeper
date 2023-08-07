@extends('admin.layout')

@section('admin-title')
    Logs
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Logs' => 'admin/logs']) !!}

    <h1>Admin Logs</h1>

    <div>
        {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::select('action', $actions, Request::get('action'), ['class' => 'form-control', 'placeholder' => 'Select Action']) !!}
        </div>
        <div class="form-group mr-3 mb-3">
            {!! Form::select('user_id', $staff, Request::get('user_id'), ['class' => 'form-control', 'placeholder' => 'Select Staff']) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}
    </div>

    {!! $logs->render() !!}
    <table class="table table-sm">
        <thead>
            <th>Staff</th>
            <th>Action</th>
            <th>Action Details</th>
            <th>Date</th>
        </thead>
        <tbody>
            @foreach ($logs as $log)
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
