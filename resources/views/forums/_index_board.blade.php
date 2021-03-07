<li class="list-group-item">
    <div class="row no-gutters">
        <div class="col-6 my-auto">
            <h5 class="mb-0">{!! $board->displayName !!}</h5>
            <p class="mb-0">{!! $board->description !!}</p>
            @if($board->accessibleSubforums->count())<p class="mb-0" style="font-size: 0.8em;">Sub-Forums: {!! implode(', ',$board->accessibleSubforums->pluck('displayName','id')->toArray()) !!}</p>@endif
        </div>
        <div class="col-3 my-auto">{!! $board->comments->whereNull('child_id')->count() !!} Topics</div>
        <div class="col-3 my-auto">
            @if($board->comments->count())
                <p class="mb-0 text-truncate"><strong>{!! $board->comments->sortByDesc('id')->first()->displayName !!}</strong></p>
                <p class="mb-0" style="font-size: 0.8em;">{!! pretty_date($board->comments->sortByDesc('id')->first()->updated_at) !!}</p>
                <p class="mb-0" style="font-size: 0.8em;">by {!! $board->comments->sortByDesc('id')->first()->commenter->displayName !!}</p>
            @else
                <p class="mb-0"> No Topics Yet. </p>
            @endif
        </div>
    </div>
</li>
