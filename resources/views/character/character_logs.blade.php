@extends('character.layout')

@section('profile-title') {{ $character->fullName }}'s Change Log @endsection

@section('profile-content')
{!! breadcrumbs(['Masterlist' => 'masterlist', $character->fullName => $character->url, 'Change Log' => $character->url.'/change-log']) !!}

@include('character._header', ['character' => $character])

<h3>Ownership History</h3>

{!! $logs->render() !!}
<table class="table table-sm">
    <thead>
        <th>Edited By</th>
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
