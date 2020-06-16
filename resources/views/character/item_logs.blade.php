@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title') {{ $character->fullName }}'s Item Logs @endsection

@section('profile-content')
{!! breadcrumbs([($character->is_myo_slot ? 'MYO Slot Masterlist' : 'Character Masterlist') => ($character->is_myo_slot ? 'myos' : 'masterlist'), $character->fullName => $character->url, "Inventory" => $character->url.'/inventory', 'Logs' => $character->url.'/item-logs']) !!}

@include('character._header', ['character' => $character])

<h3>Item Logs</h3>

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
            @include('user._item_log_row', ['log' => $log, 'owner' => $character])
        @endforeach
    </tbody>
</table>
{!! $logs->render() !!}

@endsection
