@extends('layouts.app')

@section('title') Forum :: {{ $thread->name }} @endsection

@section('content')
{!! breadcrumbs(['Forum' => 'forum' , $thread->commentable->name => 'forum/'.$thread->commentable->id, $thread->name => 'forum/'.$thread->commentable->id.'/'.$thread->id ]) !!}
<h1>{!! $thread->displayName !!}</h1>

@inject('markdown', 'Parsedown')
@php
    $markdown->setSafeMode(true);
@endphp

@if($replies->count())
    @foreach($replies as $comment)
            <div class="border mb-2 row no-gutters">
                <div class="col-3 text-center border-right">
                    <img class="mt-2" src="/images/avatars/{{ $comment->commenter->avatar }}" style="width:70px; height:70px; border-radius:50%;" alt="{{ $comment->commenter->name }} Avatar">
                    <h5>{!! $comment->commenter->displayName !!}</h5>
                    <p>{!! $comment->commenter->forumCount !!} Posts</p>
                </div>
                <div class="col">
                    <div class="mb-2 border-bottom p-2">{!! $comment->created_at->calendar() !!}</div>
                    <div class="p-2">
                        <p>{!! nl2br($markdown->line($comment->comment)) !!}</p>
                    </div>

                </div>
            </div>
    @endforeach
@else


@endif

@endsection
