<div class="row world-entry">
    @if($feature->has_image)
        <div class="col-md-3 world-entry-image"><img src="{{ $feature->imageUrl }}" class="world-entry-image" /></div>
    @endif
    <div class="{{ $feature->has_image ? 'col-md-9' : 'col-12' }}">
        <h3>{!! $feature->displayName !!} <a href="{{ $feature->searchUrl }}" class="world-entry-search text-muted"><i class="fas fa-search"></i></a></h3>
        @if($feature->feature_category_id)
            <div><strong>Category:</strong> {!! $feature->category->displayName !!}</div>
        @endif
        @if($feature->species_id)
            <div><strong>Species:</strong> {!! $feature->species->displayName !!}</div>
        @endif
        <div class="world-entry-text parsed-text">
            {!! $feature->parsed_description !!}
        </div>
    </div>
</div>