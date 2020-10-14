@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title') {{ $character->fullName }}'s Currency Logs @endsection

@section('meta-img') {{ $character->image->thumbnailUrl }} @endsection

@section('profile-content')
{!! breadcrumbs([($character->is_myo_slot ? 'MYO Slot Masterlist' : 'Character Masterlist') => ($character->is_myo_slot ? 'myos' : 'masterlist'), $character->fullName => $character->url, "Bank" => $character->url.'/bank', 'Logs' => $character->url.'/currency-logs']) !!}

@include('character._header', ['character' => $character])

<h3>Currency Logs</h3>

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
            @include('user._currency_log_row', ['log' => $log, 'owner' => $character])
        @endforeach
    </tbody>
</table>
{!! $logs->render() !!}

@endsection
