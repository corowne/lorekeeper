@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title') {{ $character->fullName }}'s Ownership History @endsection

@section('meta-img') {{ $character->image->thumbnailUrl }} @endsection

@section('profile-content')
@if($character->is_myo_slot)
{!! breadcrumbs(['MYO Slot Masterlist' => 'myos', $character->fullName => $character->url, 'Ownership History' => $character->url.'/ownership']) !!}
@else
{!! breadcrumbs([($character->category->masterlist_sub_id ? $character->category->sublist->name.' Masterlist' : 'Character masterlist') => ($character->category->masterlist_sub_id ? 'sublist/'.$character->category->sublist->key : 'masterlist' ), $character->fullName => $character->url, 'Ownership History' => $character->url.'/ownership']) !!}
@endif

@include('character._header', ['character' => $character])

<h3>Ownership History</h3>

{!! $logs->render() !!}
<div class="row ml-md-2 mb-4">
  <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-bottom">
    <div class="col-6 col-md font-weight-bold">Sender</div>
    <div class="col-6 col-md font-weight-bold">Recipient</div>
    <div class="col-6 col-md-4 font-weight-bold">Log</div>
    <div class="col-6 col-md font-weight-bold">Date</div>
  </div>
    @foreach($logs as $log)
        @include('user._ownership_log_row', ['log' => $log, 'user' => $character->user])
    @endforeach
</div>
{!! $logs->render() !!}

@endsection
