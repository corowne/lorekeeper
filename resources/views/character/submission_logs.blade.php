@extends('character.layout')

@section('profile-title') {{ $character->fullName }}'s Prompt Submissions @endsection

@section('profile-content')
{!! breadcrumbs(['Masterlist' => 'masterlist', $character->fullName => $character->url, 'Prompt Submissions' => $character->url.'/submissions']) !!}

@include('character._header', ['character' => $character])

<h3>Prompt Submissions</h3>

{!! $logs->render() !!}
<table class="table table-sm">
    <thead>
        <th>User</th>
        <th>Prompt</th>
        <th>Link</th>
        <th>Date</th>
        <th></th>
    </thead>
    <tbody>
        @foreach($logs as $log)
            <tr>
                <td>{!! $log->user->displayName !!}</td>
                <td>{!! $log->prompt->displayName !!}</td>
                <td><a href="{{ $log->url }}">{{ $log->url }}</a></td>
                <td>{{ format_date($log->created_at) }}</td>
                <td class="text-right"><a href="{{ $log->viewUrl }}" class="btn btn-primary btn-sm">Details</a></td>
            </tr>
        @endforeach
    </tbody>
</table>
{!! $logs->render() !!}

@endsection
