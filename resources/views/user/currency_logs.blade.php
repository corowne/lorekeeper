@extends('user.layout')

@section('profile-title') {{ $user->name }}'s Currency Logs @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Bank' => $user->url . '/bank', 'Logs' => $user->url.'/currency-logs']) !!}

<h1>
    {!! $user->displayName !!}'s Currency Logs
</h1>

{!! $logs->render() !!}
<table class="table table-sm">
    <thead>
        <th>Sender</th>
        <th>Recipient</th>
        <th>Currency</th>
        <th>Log</th>
        <th>Date</th>
    </thead>
    <tbody>
        @foreach($logs as $log)
            @include('user._currency_log_row', ['log' => $log, 'owner' => $user])
        @endforeach
    </tbody>
</table>
{!! $logs->render() !!}

@endsection
