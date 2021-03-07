

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
