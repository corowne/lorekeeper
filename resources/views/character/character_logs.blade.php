@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title') {{ $character->fullName }}'s Change Log @endsection

@section('meta-img') {{ $character->image->thumbnailUrl }} @endsection

@section('profile-content')
{!! breadcrumbs([($character->is_myo_slot ? 'MYO Slot Masterlist' : 'Character Masterlist') => ($character->is_myo_slot ? 'myos' : 'masterlist'), $character->fullName => $character->url, 'Change Log' => $character->url.'/change-log']) !!}

@include('character._header', ['character' => $character])

<h3>Change Log</h3>

{!! $logs->render() !!}
<table class="table table-sm">
    <thead>
        <th>Edited By</th>
        <th>Log</th>
        <th>Date</th>
    </thead>
    <tbody>
        @foreach($logs as $log)
            @include('character._character_log_row', ['log' => $log, 'character' => $character])
        @endforeach
    </tbody>
</table>
{!! $logs->render() !!}

@endsection
