@if($forums->count())
    <div class="card mb-3">
        <ul class="list-group list-group-flush">
            @foreach($forums as $forum)
                <li class="list-group-item">
                    <div class="row no-gutters">
                        <div class="col-6 my-auto">
                            <h5 class="mb-0">{!! $forum->displayName !!}</h5>
                            <p class="mb-0">{!! $forum->description !!}</p>
                            @if($forum->children->count())<p class="mb-0" style="font-size: 0.8em;">Subboards: {!! implode(', ',$forum->children->pluck('displayName','id')->toArray()) !!}</p>@endif
                        </div>
                        <div class="col-3 my-auto">{!! $forum->comments->whereNull('child_id')->count() !!} Threads</div>
                        <div class="col-3 my-auto">
                            @if($forum->comments->count())
                                <p class="mb-0 text-truncate"><strong>{!! $forum->comments->sortByDesc('id')->first()->displayName !!}</strong></p>
                                <p class="mb-0" style="font-size: 0.8em;">{!! pretty_date($forum->comments->sortByDesc('id')->first()->updated_at) !!}</p>
                                <p class="mb-0" style="font-size: 0.8em;">by {!! $forum->comments->sortByDesc('id')->first()->commenter->displayName !!}</p>
                            @else
                                <p class="mb-0"> No Threads Yet. </p>
                            @endif
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@endif
