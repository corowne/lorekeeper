<div class="row world-entry">
    @if($currency->has_image)
        <div class="col-md-3 world-entry-image"><a href="{{ $currency->currencyImageUrl }}" data-lightbox="entry" data-title="{{ $currency->name }}"><img src="{{ $currency->currencyImageUrl }}" class="world-entry-image" alt="{{ $currency->name }}" /></a></div>
    @endif
    <div class="{{ $currency->has_image ? 'col-md-9' : 'col-12' }}">
        <h3>{{ $currency->name }} @if($currency->abbreviation) ({{ $currency->abbreviation }}) @endif</h3>
        <div><strong>Displays as:</strong> {!! $currency->display(0) !!}</div>
        <div><strong>Held by:</strong> <?php echo ucfirst(implode(' and ', ($currency->is_user_owned ? ['users'] : []) + ($currency->is_character_owned ? ['characters'] : []))); ?></div>
        <div class="world-entry-text parsed-text">
            {!! $currency->parsed_description !!}
        </div>
    </div>
</div>
