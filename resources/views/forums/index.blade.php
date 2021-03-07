@extends('layouts.app')

@section('title') Forum @endsection

@section('content')
{!! breadcrumbs(['Forum' => 'forum']) !!}
<h1>Forums</h1>

@if(count($forums))

    @foreach($forums as $forum)
        @if($forum->children->where('is_active',1)->count())
            <div class="card mb-3">
                <div class="card-body px-3 py-2">
                    <h3 class="mb-0" data-toggle="tooltip" title="{!! $forum->description !!}">{!! $forum->displayName !!} </h3>
                </div>
                <ul class="list-group list-group-flush">
                    @foreach($forum->children->sortBy('id')->sortBy('sort') as $board)
                        @if($board->hasRestrictions && Auth::check() && Auth::user()->canVisitForum($board->id))
                            @include('forums._index_board', ['board' => $board])
                        @elseif(!$board->hasRestrictions)
                            @include('forums._index_board', ['board' => $board])
                        @endif
                    @endforeach
                </ul>
            </div>
        @endif
    @endforeach

@else
    <div>No forums yet.</div>
@endif

@endsection
