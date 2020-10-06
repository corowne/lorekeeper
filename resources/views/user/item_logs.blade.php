@extends('user.layout')

@section('profile-title') {{ $user->name }}'s Item Logs @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Inventory' => $user->url . '/inventory', 'Logs' => $user->url.'/item-logs']) !!}

<h1>
    {!! $user->displayName !!}'s Item Logs
</h1>

{!! $logs->render() !!}
<table class="table table-sm">
    <thead>
        <th>Sender</th>
        <th>Recipient</th>
        <th>Item</th>
        <th>Log</th>
        <th>Date</th>
    </thead>
    <tbody>
        @foreach($logs as $log)
            @include('user._item_log_row', ['log' => $log, 'owner' => $user])
        @endforeach
    </tbody>
</table>
{!! $logs->render() !!}

@endsection
