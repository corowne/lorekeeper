<div class="row world-entry">
    @if($subtype->subtypeImageUrl)
        <div class="col-md-3 world-entry-image"><a href="{{ $subtype->subtypeImageUrl }}" data-lightbox="entry" data-title="{{ $subtype->name }}"><img src="{{ $subtype->subtypeImageUrl }}" class="world-entry-image" alt="{{ $subtype->name }}" /></a></div>
    @endif
    <div class="{{ $subtype->subtypeImageUrl ? 'col-md-9' : 'col-12' }}">
        <h3>{!! $subtype->displayName !!} ({!! $subtype->species->displayName !!} Subtype) <a href="{{ $subtype->searchUrl }}" class="world-entry-search text-muted"><i class="fas fa-search"></i></a></h3>
        <div class="world-entry-text">
            {!! $subtype->parsed_description !!}
        </div>
    </div>
</div>
