@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title') {{ $character->slug }}'s Level @endsection

@section('profile-content')
{!! breadcrumbs(['Characters' => 'characters', $character->slug => $character->url, 'Level' => $character->url . '/level']) !!}

<h1>
    {!! $character->displayName !!}'s Level Logs
</h1>

<div class="mb-4 text-center">
    <div class="card text-center">
        <div class="m-4"><strong>Level:</strong> <br>{{ $character->level->current_level }}</div>
        <div class="m-4"><strong>Current EXP:</strong> <br>{{ $character->level->current_exp }} </div>
        <div class="m-4"><strong>Current Available Stat Points:</strong> <br>{{ $character->level->current_points }}</div>
    </div>
</div>

<h3>Latest EXP Activity</h3>
<table class="table table-sm">
    <thead>
        <th>Sender</th>
        <th>Recipient</th>
        <th>Quantity</th>
        <th>Log</th>
        <th>Date</th>
   </thead>
    <tbody>
        @foreach($exps as $exp)
            @include('character.stats._exp_log_row', ['exp' => $exp, 'owner' => $character])
        @endforeach
    </tbody>
</table>
<div class="text-right">
    <a href="{{ url($character->url.'/exp-logs') }}">View all...</a>
</div>

<h3>Latest Stat Transfer Activity</h3>
<table class="table table-sm">
    <thead>
        <th>Sender</th>
        <th>Recipient</th>
        <th>Quantity</th>
        <th>Log</th>
        <th>Date</th>
   </thead>
    <tbody>
        @foreach($stats as $stat)
            @include('character.stats._stat_log_row', ['exp' => $stat, 'owner' => $character])
        @endforeach
    </tbody>
</table>
<div class="text-right">
    <a href="{{ url($character->url.'/stat-logs') }}">View all...</a>
</div>

<h3>Latest Level-Up Activity</h3>
<table class="table table-sm">
    <thead>
        <th></th>
        <th>Old Level</th>
        <th>New Level</th>
        <th>Date</th>
   </thead>
    <tbody>
        @foreach($levels as $level)
            @include('character.stats._level_log_row', ['exp' => $level, 'owner' => $character])
        @endforeach
    </tbody>
</table>
<div class="text-right">
    <a href="{{ url($character->url.'/level-logs') }}">View all...</a>
</div>

<h3>Latest Current Count Activity</h3>
<table class="table table-sm">
    <thead>
        <th>Sender</th>
        <th>Recipient</th>
        <th>Quantity</th>
        <th>Log</th>
        <th>Date</th>
   </thead>
    <tbody>
        @foreach($counts as $count)
            @include('character.stats._count_log_row', ['count' => $count, 'owner' => $character])
        @endforeach
    </tbody>
</table>
<div class="text-right">
    <a href="{{ url($character->url.'/count-logs') }}">View all...</a>
</div>

@endsection
