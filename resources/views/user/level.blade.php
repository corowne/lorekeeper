@extends('user.layout')

@section('profile-title') {{ $user->name }}'s Level @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Level' => $user->url . '/level']) !!}

<h1>
    {!! $user->displayName !!}'s Level Logs
</h1>

<div class="mb-4 text-center">
    <div class="card text-center">
        <div class="m-4"><strong>Level:</strong> <br>{{ $user->level->current_level }}</div>
        <div class="m-4"><strong>Current EXP:</strong> <br>{{ $user->level->current_exp }} </div>
        <div class="m-4"><strong>Current Available Stat Points:</strong> <br>{{ $user->level->current_points }}</div>
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
            @include('user._exp_log_row', ['exp' => $exp, 'owner' => $user])
        @endforeach
    </tbody>
</table>
<div class="text-right">
    <a href="{{ url($user->url.'/exp-logs') }}">View all...</a>
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
            @include('user._stat_log_row', ['exp' => $stat, 'owner' => $user])
        @endforeach
    </tbody>
</table>
<div class="text-right">
    <a href="{{ url($user->url.'/stat-logs') }}">View all...</a>
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
            @include('user._level_log_row', ['exp' => $level, 'owner' => $user])
        @endforeach
    </tbody>
</table>
<div class="text-right">
    <a href="{{ url($user->url.'/level-logs') }}">View all...</a>
</div>

@endsection
