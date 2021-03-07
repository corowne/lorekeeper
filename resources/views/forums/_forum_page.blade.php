@if(!$forum->is_locked)<div class="float-right mb-2"><a class="btn btn-primary" href="{{ url('forum/'.$forum->id.'/new') }}">New Thread</a></div>@endif
@include('forums._forum_topper', ['forum' => $forum])
@if($forum->accessibleSubforums->count())
    <div class="row no-gutters mb-2" style="clear:both;">
        <h5 class="col-12">Subforums:</h5>
        @foreach($forum->accessibleSubforums as $board)
            <div class=" {{ $loop->even ? 'px-2' : '' }} col-md-4"><div class="card p-2 h-100">
                {!! $board->displayName !!}
                @if($board->accessibleSubforums->count())<p class="mb-0" style="font-size: 0.8em;">Sub-Forums: {!! implode(', ',$board->accessibleSubforums->pluck('displayName','id')->toArray()) !!}</p>@endif
            </div></div>
        @endforeach
    </div>
@endif

@if($posts->count())
    <div class="card mb-2" style="clear:both;">
        <ul class="list-group list-group-flush">
            @foreach($posts as $comment)
                <li class="list-group-item">
                    <div class="row">
                        <div class="d-none d-md-flex col-md-auto my-auto">
                            <i class="fas {{ $comment->is_featured ? 'fa-thumbtack' : ($comment->is_locked ? 'fa-lock' : 'fa-circle') }}"></i>
                        </div>
                        <div class="col-12 col-md my-auto">
                            <p class="mb-0"><strong>{!! $comment->displayName !!}</strong></p>
                            <p class="mb-0">by {!! $comment->commenter->displayName !!}  - {!! pretty_date($comment->created_at) !!}</p>
                        </div>
                        <div class="col-6 col-md-3 my-auto">
                            <p class="mb-0"> {{ $comment->getAllChildren()->count() }} Replies</p>
                        </div>
                        <div class="col-6 col-md-3 my-auto">
                            <p class="mb-0">@if(isset($comment->latestReply)) <span class="d-none d-md-inline">Latest reply by {!! $comment->latestReply->commenter->displayName !!} - </span>{!! pretty_date($comment->latestReply->updated_at) !!} @else No Replies @endif</p>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
    {!! $posts->render() !!}
@else
    <div class="card" style="clear:both;">
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                No Threads
            </li>
        </ul>
    </div>
@endif
