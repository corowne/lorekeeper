@php
    if (isset($approved) and $approved == true) {
        if(isset($type) && $type != null) $comments = $model->approvedComments->where('type', $type);
        else $comments = $model->approvedComments->where('type', "User-User");
    } else {
        if(isset($type) && $type != null) $comments = $model->commentz->where('type', $type);
        else $comments = $model->commentz->where('type', "User-User");
    }
@endphp

@if(!isset($type) || $type == "User-User")
    <h2>Comments</h2>
@endif
<div class="d-flex mw-100 row mx-0" style="overflow:hidden;">
    @php
        $comments = $comments->sortByDesc('created_at');

        if (isset($perPage)) {
            $page = request()->query('page', 1) - 1;

            $parentComments = $comments->where('child_id', '');

            $slicedParentComments = $parentComments->slice($page * $perPage, $perPage);

            $m = Config::get('comments.model'); // This has to be done like this, otherwise it will complain.
            $modelKeyName = (new $m)->getKeyName(); // This defaults to 'id' if not changed.

            $slicedParentCommentsIds = $slicedParentComments->pluck($modelKeyName)->toArray();

            // Remove parent Comments from comments.
            $comments = $comments->where('child_id', '!=', '');

            $grouped_comments = new \Illuminate\Pagination\LengthAwarePaginator(
                $slicedParentComments->merge($comments)->groupBy('child_id'),
                $parentComments->count(),
                $perPage
            );

            $grouped_comments->withPath(request()->url());
        } else {
            $grouped_comments = $comments->groupBy('child_id');
        }
    @endphp
    @foreach($grouped_comments as $comment_id => $comments)
        {{-- Process parent nodes --}}
        @if($comment_id == '')
            @foreach($comments as $comment)
                @include('comments::_comment', [
                    'comment' => $comment,
                    'grouped_comments' => $grouped_comments,
                    'limit' => 0,
                    'compact' => ($comment->type == "Staff-Staff") ? true : false,
                ])
            @endforeach
        @endif
    @endforeach
</div>

@if($comments->count() < 1)
    <div class="alert alert-warning">There are no comments yet.</div>
@endif

@isset ($perPage)
    <div class="ml-auto mt-2">{{ $grouped_comments->links() }}</div>
@endisset

@auth
    @include('comments._form')
@else
    <div class="card mt-3">
        <div class="card-body">
            <h5 class="card-title">Authentication required</h5>
            <p class="card-text">You must log in to post a comment.</p>
            <a href="{{ route('login') }}" class="btn btn-primary">Log in</a>
        </div>
    </div>
@endauth
