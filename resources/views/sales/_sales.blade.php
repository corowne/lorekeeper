<div class="card mb-3">
    <div class="card-header">
        <h2 class="card-title mb-0">{!! $sales->displayName !!}</h2>
        <small>
            Posted {!! $sales->post_at ? format_date($sales->post_at) : format_date($sales->created_at) !!} by {!! $sales->user->displayName !!}
        </small>
    </div>
    <div class="card-body">
        <div class="parsed-text">
            {!! $sales->parsed_text !!}
        </div>
    </div>
</div>