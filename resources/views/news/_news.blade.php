<div class="card">
    <div class="card-header">
        <h2 class="card-title mb-0">{!! $news->displayName !!}</h2>
        <small>
            Posted {{ $news->post_at ? format_date($news->post_at) : format_date($news->created_at) }} by {!! $news->user->displayName !!}
        </small>
    </div>
    <div class="card-body">
        <div class="parsed-text">
            {!! $news->parsed_text !!}
        </div>
    </div>
</div>