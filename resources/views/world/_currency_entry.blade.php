<div class="row world-entry">
    @if ($currency->has_image)
        <div class="col-md-3 world-entry-image"><a href="{{ $currency->currencyImageUrl }}" data-lightbox="entry" data-title="{{ $currency->name }}"><img src="{{ $currency->currencyImageUrl }}" class="world-entry-image" alt="{{ $currency->name }}" /></a>
        </div>
    @endif
    <div class="{{ $currency->has_image ? 'col-md-9' : 'col-12' }}">
        <x-admin-edit title="Currency" :object="$currency" />
        <h3>{{ $currency->name }} @if ($currency->abbreviation)
                ({{ $currency->abbreviation }})
            @endif
            @if(Auth::check())
                @include('widgets._item_block', ['item' => $currency, 'float' => true])
            @endif
        </h3>
        <div><strong>Displays as:</strong> {!! $currency->display(0) !!}</div>
        <div><strong>Held by:</strong> <?php echo ucfirst(implode(' and ', ($currency->is_user_owned ? ['users'] : []) + ($currency->is_character_owned ? ['characters'] : []))); ?></div>
        @if ($currency->conversions()->count())
            <div class="world-entry-text">
                <h5>Conversion Rates</h5>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>From</th>
                            <th>To</th>
                            <th width="10%">Ratio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($currency->conversions as $conversion)
                            <tr>
                                <td>{!! $conversion->currency->display($conversion->ratio(true)[0]) !!}</td>
                                <td>{!! $conversion->convert->display($conversion->ratio(true)[1]) !!}</td>
                                <td>{{ $conversion->ratio() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        <div class="world-entry-text parsed-text">
            {!! $currency->parsed_description !!}
        </div>
    </div>
</div>
