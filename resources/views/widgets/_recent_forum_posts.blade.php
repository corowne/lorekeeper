@php
    $forums = \App\Models\Forum::all();
    $public = [];
    $posts = collect();

    if(Auth::check())
    {
        foreach($forums as $key => $forum)
        {
            if(Auth::user()->canVisitForum($forum->id) && ($forum->parent ? (Auth::user()->canVisitForum($forum->parent->id) && ($forum->parent->parent ? Auth::user()->canVisitForum($forum->parent->parent->id) : true) ) : true)) $public[] = $forum->id;
        }
        $posts = \App\Models\Comment::with('parent')->where('commentable_type','App\Models\Forum')->orderBy('created_at', 'DESC')->get()->whereIn('commentable_id',$public)->take(5);
    }

@endphp

<div class="card mb-4">
        <h5 class="card-title text-center my-2">Recent Forum Posts</h5>
    <ul class="list-group list-group-flush">
        @if($posts->count())
            @foreach($posts as $post)
                <li class="list-group-item py-1">
                    {!! $post->displayName !!}
                     in <strong>{!! $post->commentable->displayName !!}</strong>
                     by {!! $post->commenter->displayName !!}
                </li>
            @endforeach
        @else
            <li class="list-group-item py-1"> No forum posts. </li>
        @endif
    </ul>
</div>
