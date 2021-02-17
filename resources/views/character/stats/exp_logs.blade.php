@extends('character.layout')

@section('profile-title') {{ $character->slug }}'s EXP Logs @endsection

@section('profile-content')
{!! breadcrumbs(['Characters' => 'characters', $character->slug => $character->url, 'Level' => $character->url . '/level', 'Logs' => $character->url.'/exp-logs']) !!}

<h1>
    {!! $character->displayName !!}'s EXP Logs
</h1>

{!! $logs->render() !!}
<table class="table table-sm">
    <thead>
        <th>Sender</th>
        <th>Recipient</th>
        <th>Quantity</th>
        <th>Log</th>
        <th>Date</th>
    </thead>
    <tbody>
        @foreach($logs as $log)
            @include('character._exp_log_row', ['exp' => $log, 'owner' => $character])
        @endforeach
    </tbody>
</table>
{!! $logs->render() !!}

@endsection