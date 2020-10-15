@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title') {{ $character->fullName }}'s Ownership History @endsection

@section('profile-content')
@if($character->category->masterlist_sub_id != 0 && $character->category->sublist->show_main == 0)
{!! breadcrumbs([($character->is_myo_slot ? 'MYO Slot Masterlist' : $character->category->sublist->name.' Masterlist') => ($character->is_myo_slot ? 'myos' : 'sublist/'.$character->category->sublist->key), $character->fullName => $character->url,  'Ownership History' => $character->url.'/ownership']) !!}
@else
{!! breadcrumbs([($character->is_myo_slot ? 'MYO Slot Masterlist' : 'Character Masterlist') => ($character->is_myo_slot ? 'myos' : 'masterlist'), $character->fullName => $character->url,  'Ownership History' => $character->url.'/ownership']) !!}
@endif

@include('character._header', ['character' => $character])

<h3>Ownership History</h3>

{!! $logs->render() !!}
<table class="table table-sm">
    <thead>
        <th>Sender</th>
        <th>Recipient</th>
        <th>Log</th>
        <th>Date</th>
    </thead>
    <tbody>
        @foreach($logs as $log)
            @include('user._ownership_log_row', ['log' => $log, 'user' => $character->user])
        @endforeach
    </tbody>
</table>
{!! $logs->render() !!}

@endsection
