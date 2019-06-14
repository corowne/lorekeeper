@extends('user.layout')

@section('profile-title') {{ $user->name }}'s Character History @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Inventory' => $user->url . '/inventory', 'Logs' => $user->url.'/ownership']) !!}

<h1>
    {!! $user->displayName !!}'s Character History
</h1>

{!! $logs->render() !!}
<table class="table table-sm">
    <thead>
        <th>Sender</th>
        <th>Recipient</th>
        <th>Character</th>
        <th>Log</th>
        <th>Date</th>
    </thead>
    <tbody>
        @foreach($logs as $log)
            {!! $log->displayRow($user) !!}
        @endforeach
    </tbody>
</table>
{!! $logs->render() !!}

@endsection
