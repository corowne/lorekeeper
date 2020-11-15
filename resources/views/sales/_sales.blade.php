<div class="card mb-3">
    <div class="card-header">
        <h2 class="card-title mb-0">{!! $sales->displayName !!}</h2>
        <small>
            Posted {!! $sales->post_at ? pretty_date($sales->post_at) : pretty_date($sales->created_at) !!} :: Last edited {!! pretty_date($sales->updated_at) !!} by {!! $sales->user->displayName !!}
        </small>
    </div>
    <div class="card-body">
        <div class="parsed-text">
            {!! $sales->parsed_text !!}
        </div>
    </div>
    @if((isset($sales->comments_open_at) && $sales->comments_open_at < Carbon\Carbon::now() || 
    (Auth::check() && Auth::user()->hasPower('edit_pages'))) || 
    !isset($sales->comments_open_at))
        <?php $commentCount = App\Models\Comment::where('commentable_type', 'App\Models\Sales')->where('commentable_id', $sales->id)->count(); ?>
        @if(!$page)
            <div class="text-right mb-2 mr-2">
                <a class="btn" href="{{ $sales->url }}"><i class="fas fa-comment"></i> {{ $commentCount }} Comment{{ $commentCount != 1 ? 's' : ''}}</a>
            </div>
        @else
            <div class="text-right mb-2 mr-2">
                <span class="btn"><i class="fas fa-comment"></i> {{ $commentCount }} Comment{{ $commentCount != 1 ? 's' : ''}}</span>
            </div>
        @endif
    @endif
</div>