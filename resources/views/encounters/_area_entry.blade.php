<div class="col-md-3 col-6 mb-3 text-center ">
    @if ($area->has_thumbnail)
        <div class="shop-image">
            <a href="{{ $area->url }}"><img src="{{ $area->thumbImageUrl }}" alt="{{ $area->name }}" /></a>
        </div>
    @endif
    <div class="shop-name mt-1">
        <a href="{{ $area->url }}" class="h5 mb-0">
            {{ $area->name }}
        </a>
    </div>
    <div class="shop-text mt-1">
        {!! $area->parsed_description !!}
    </div>
    @if ($area->limits->count())
        <div class="text-muted small">(Requires <?php
        $limits = [];
        foreach ($area->limits as $limit) {
            $name = $limit->item->displayName;
            $limits[] = $name;
        }
        echo implode(', ', $limits);
        ?>)</div>
    @endif
</div>
