@extends('home.layout')

@section('home-title')
    My Liked Comments
@endsection

@section('home-content')
    {!! breadcrumbs(['Liked Comments' => 'liked-comments']) !!}

    <h1>
        My Liked Comments
    </h1>

    <p>This is a list of comments you've liked or disliked. This list is not public, but users can see on the comment who has liked it.</p>
    <div class="p-2">
        {{-- Order user CommentLikes by when the comment was created inside the foreach --}}
        @php
            $comments = $user->commentLikes;
            $comments = $comments->sortByDesc(function ($comment) {
                return $comment->comment->created_at;
            });
        @endphp

        @foreach ($comments as $comment)
            <div class="card col-12 mb-2">
                @include('comments._perma_comments', ['comment' => $comment->comment, ($limit = 0), ($depth = 0)])
            </div>
        @endforeach
    </div>
@endsection
