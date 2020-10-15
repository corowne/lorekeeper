@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title') {{ $character->fullName }}'s Submissions @endsection

@section('profile-content')
@if($character->category->masterlist_sub_id != 0 && $character->category->sublist->show_main == 0)
{!! breadcrumbs([($character->is_myo_slot ? 'MYO Slot Masterlist' : $character->category->sublist->name.' Masterlist') => ($character->is_myo_slot ? 'myos' : 'sublist/'.$character->category->sublist->key), $character->fullName => $character->url,  'Submissions' => $character->url.'/submissions']) !!}
@else
{!! breadcrumbs([($character->is_myo_slot ? 'MYO Slot Masterlist' : 'Character Masterlist') => ($character->is_myo_slot ? 'myos' : 'masterlist'), $character->fullName => $character->url,  'Submissions' => $character->url.'/submissions']) !!}
@endif

@include('character._header', ['character' => $character])

<h3>Submissions</h3>

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
                <td>{!! $log->prompt_id ? $log->prompt->displayName : '---' !!}</td>
                <td class="text-break"><a href="{{ $log->url }}">{{ $log->url }}</a></td>
                <td>{!! format_date($log->created_at) !!}</td>
                <td class="text-right"><a href="{{ $log->viewUrl }}" class="btn btn-primary btn-sm">Details</a></td>
            </tr>
        @endforeach
    </tbody>
</table>
{!! $logs->render() !!}

@endsection
