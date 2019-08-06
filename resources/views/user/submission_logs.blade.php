@extends('user.layout')

@section('profile-title') {{ $user->name }}'s Prompt Submissions @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Prompt Submissions' => $user->url.'/submissions']) !!}

<h1>
    {!! $user->displayName !!}'s Prompt Submissions
</h1>

{!! $logs->render() !!}
<table class="table table-sm">
    <thead>
        <th>Prompt</th>
        <th>Link</th>
        <th>Date</th>
        <th></th>
    </thead>
    <tbody>
        @foreach($logs as $log)
            <tr>
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
