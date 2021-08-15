@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title') {{ $character->slug }}'s Level Logs @endsection

@section('profile-content')
{!! breadcrumbs(['Characters' => 'characters', $character->slug => $character->url, 'Level' => $character->url . '/level', 'Logs' => $character->url.'/level-logs']) !!}

<h1>
    {!! $character->displayName !!}'s Level Logs
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
            @include('character.stats._level_log_row', ['level' => $log, 'owner' => $character])
        @endforeach
    </tbody>
</table>
{!! $logs->render() !!}

@endsection