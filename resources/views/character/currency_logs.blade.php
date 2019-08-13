@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title') {{ $character->fullName }}'s Currency Logs @endsection

@section('profile-content')
{!! breadcrumbs(['Masterlist' => 'masterlist', $character->fullName => $character->url, $character->fullName."'s Bank" => $character->url.'/bank', 'Logs' => $character->url.'/currency-logs']) !!}

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
            {!! $log->displayRow($character) !!}
        @endforeach
    </tbody>
</table>
{!! $logs->render() !!}

@endsection
