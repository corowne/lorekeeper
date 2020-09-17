@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title') {{ $character->fullName }}'s Submissions @endsection

@section('meta-img') {{ $character->image->thumbnailUrl }} @endsection

@section('profile-content')
{!! breadcrumbs([($character->is_myo_slot ? 'MYO Slot Masterlist' : 'Character Masterlist') => ($character->is_myo_slot ? 'myos' : 'masterlist'), $character->fullName => $character->url, 'Submissions' => $character->url.'/submissions']) !!}

@include('character._header', ['character' => $character])

<h3>Submissions</h3>

@if(count($logs))

{!! $logs->render() !!}

<div class="row ml-md-2">
  <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-bottom">
    <div class="col-12 col-md-2 font-weight-bold">Submitted By</div>
    <div class="col-6 col-md-2 font-weight-bold">Prompt</div>
    <div class="col-6 col-md-4 font-weight-bold">Link</div>
    <div class="col-6 col-md-3 font-weight-bold">Date</div>
  </div>

  @foreach($logs as $log)
    <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
      <div class="col-12 col-md-2">{!! $log->user->displayName !!}</div>
      <div class="col-6 col-md-2">{!! $log->prompt_id ? $log->prompt->displayName : '---' !!}</div>
      <div class="col-6 col-md-4">
        <span class="ubt-texthide"><a href="{{ $log->url }}">{{ $log->url }}</a></span>
      </div>
      <div class="col-6 col-md-3">{!! pretty_date($log->created_at) !!}</div>
      <div class="col-6 col-md-1"><a href="{{ $log->viewUrl }}" class="btn btn-primary btn-sm py-0 px-1">Details</a></div>
    </div>
  @endforeach
</div>

{!! $logs->render() !!}

@else
    <p>No submissions found.</p>
@endif

@endsection
