@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title') {{ $character->slug }}'s Stat Logs @endsection

@section('profile-content')
{!! breadcrumbs(['Characters' => 'characters', $character->slug => $character->url, 'Level' => $character->url . '/level', 'Logs' => $character->url.'/stat-logs']) !!}

<h1>
    {!! $character->displayName !!}'s Stat Logs
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
            @include('character.stats._stat_log_row', ['stat' => $log, 'owner' => $character])
        @endforeach
    </tbody>
</table>
{!! $logs->render() !!}

@endsection