@extends('user.layout')

@section('profile-title') {{ $user->name }}'s Submissions @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Submissions' => $user->url.'/submissions']) !!}

<h1>
    {!! $user->displayName !!}'s Submissions
</h1>

{!! $logs->render() !!}
<div class="row ml-md-2">
  <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-bottom">
    <div class="col-12 col-md-2 font-weight-bold">Prompt</div>
    <div class="col-6 col-md-4 font-weight-bold">Link</div>
    <div class="col-6 col-md-5 font-weight-bold">Date</div>
  </div>

  @foreach($logs as $log)
    <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
      <div class="col-12 col-md-2">{!! $log->prompt_id ? $log->prompt->displayName : '---' !!}</div>
      <div class="col-6 col-md-4">
        <span class="ubt-texthide"><a href="{{ $log->url }}">{{ $log->url }}</a></span>
      </div>
      <div class="col-6 col-md-5">{!! pretty_date($log->created_at) !!}</div>
      <div class="col-6 col-md-1"><a href="{{ $log->viewUrl }}" class="btn btn-primary btn-sm py-0 px-1">Details</a></div>
    </div>
  @endforeach
  </div>
{!! $logs->render() !!}

@endsection
