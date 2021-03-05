<div class="float-right mb-2"><a class="btn btn-primary" href="{{ url('forum/'.$forum->id.'/new') }}">New Thread</a></div>

@if($posts->count())
    <div class="card mb-2" style="clear:both;">
        <ul class="list-group list-group-flush">
            @foreach($posts as $comment)
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-6 my-auto">
                            <p class="mb-0"><strong>{!! $comment->displayName !!}</strong></p>
                            <p class="mb-0">by {!! $comment->commenter->displayName !!}  - {!! pretty_date($comment->created_at) !!}</p>
                        </div>
                        <div class="col-3 my-auto">
                            <p class="mb-0"> {{ $comment->getAllChildren()->count() }} Replies</p>
                        </div>
                        <div class="col-3 my-auto">
                            <p class="mb-0">@if(isset($comment->latestReply)) Latest reply by {!! $comment->latestReply->commenter->displayName !!} - {!! pretty_date($comment->latestReply->updated_at) !!} @else No Replies @endif</p>
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
