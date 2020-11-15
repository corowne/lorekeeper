@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title') {{ $character->fullName }}'s Currency Logs @endsection

@section('meta-img') {{ $character->image->thumbnailUrl }} @endsection

@section('profile-content')
{!! breadcrumbs([($character->category->masterlist_sub_id ? $character->category->sublist->name.' Masterlist' : 'Character masterlist') => ($character->category->masterlist_sub_id ? 'sublist/'.$character->category->sublist->key : 'masterlist' ), $character->fullName => $character->url,  "Bank" => $character->url.'/bank', 'Logs' => $character->url.'/currency-logs']) !!}

@include('character._header', ['character' => $character])

<h3>Currency Logs</h3>

{!! $logs->render() !!}

<div class="row ml-md-2">
  <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-bottom">
    <div class="col-6 col-md-2 font-weight-bold">Sender</div>
    <div class="col-6 col-md-2 font-weight-bold">Recipient</div>
    <div class="col-6 col-md-2 font-weight-bold">Currency</div>
    <div class="col-6 col-md-4 font-weight-bold">Log</div>
    <div class="col-6 col-md-2 font-weight-bold">Date</div>
  </div>
    @foreach($logs as $log)
        @include('user._currency_log_row', ['log' => $log, 'owner' => $character])
    @endforeach
</div>
{!! $logs->render() !!}

@endsection
