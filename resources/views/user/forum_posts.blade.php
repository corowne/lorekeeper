@extends('user.layout')

@section('profile-title') {{ $user->name }}'s Forum Posts @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Forum Posts' => $user->url.'/forum']) !!}

<h1>
    {!! $user->displayName !!}'s Forum Posts
</h1>

{!! $posts->render() !!}
<div class="row ml-md-2 mb-4">
  <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-bottom">
    <div class="col-12 col-md font-weight-bold">Thread</div>
    <div class="col col-md-2 font-weight-bold">Forum</div>
    <div class="col col-md-2 font-weight-bold">Posted At</div>
  </div>
  @foreach($posts as $post)

  <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
    <div class="col-12 col-md">{!! $post->displayName !!}</div>
    <div class="col col-md-2">{!! $post->commentable->displayName !!}</div>
    <div class="col col-md-2">{!! $post->created_at->calendar() !!}</div>
  </div>
  @endforeach
</div>
{!! $posts->render() !!}

@endsection
