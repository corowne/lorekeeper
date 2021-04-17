@if(!$stock)
    <div class="text-center">Invalid item selected.</div>
@else
    <div class="text-center mb-3">
        <div class="mb-1"><a href="{{ $stock->item->idUrl }}"><img src="{{ $stock->item->imageUrl }}" /></a></div>
        <div><a href="{{ $stock->item->idUrl }}"><strong>{{ $stock->item->name }}</strong></a></div>
        <div>Stock: {{ $stock->stock }}</div>
    </div>

    <div class="mb-2">
        <a data-toggle="collapse" href="#itemDescription" class="h5">Description <i class="fas fa-caret-down"></i></a>
        <div class="card collapse show mt-1" id="itemDescription">
            <div class="card-body">
                {!! $stock->item->parsed_description !!}
                {!! $stock->item->parsed_description ? '<hr/>' : ''!!}
                <p>
                    This item was generously donated by {!! $stock->stack->user->displayName !!}!
                </p>
                <div class="row">
                    @if(isset($stock->stack->data['data']))
                        <div class="col-md">
                            <strong>Source:</strong> {!! $stock->stack->data['data'] !!}
                        </div>
                    @endif
                    @if(isset($stock->stack->data['notes']))
                        <div class="col-md">
                            <strong>Notes:</strong> {!! $stock->stack->data['notes'] !!}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(Auth::check())
        @if($stock->stock == 0)
            <div class="alert alert-warning mb-0">This item is out of stock.</div>
        @else
        <p>You may collect <strong>one (1)</strong> item from this shop every {{ Config::get('lorekeeper.settings.donation_shop.cooldown') }} minutes.</p>
            {!! Form::open(['url' => 'shops/collect']) !!}
                {!! Form::hidden('stock_id', $stock->id) !!}
                <div class="text-right">
                    {!! Form::submit('Collect', ['class' => 'btn btn-primary']) !!}
                </div>
            {!! Form::close() !!}
        @endif
    @else
        <div class="alert alert-danger">You must be logged in to collect this item.</div>
    @endif
@endif
