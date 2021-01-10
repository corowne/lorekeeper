@extends('user.layout')

@section('profile-title') {{ $user->name }}'s Level Logs @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Level' => $user->url . '/level', 'Logs' => $user->url.'/level-logs']) !!}

<h1>
    {!! $user->displayName !!}'s Level Logs
</h1>

{!! $logs->render() !!}
<table class="table table-sm">
    <thead>
        <th></th>
        <th>Old Level</th>
        <th>New Level</th>
        <th>Date</th>
    </thead>
    <tbody>
        @foreach($logs as $log)
            @include('user._level_log_row', ['level' => $log, 'owner' => $user])
        @endforeach
    </tbody>
</table>
{!! $logs->render() !!}

@endsection