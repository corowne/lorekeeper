@if(!$stock)
    <div class="text-center">Invalid item selected.</div>
@else
    <div class="text-center mb-3">
        <div class="mb-1"><a href="{{ $stock->item->idUrl }}"><img src="{{ $stock->item->imageUrl }}" /></a></div>
        <div><a href="{{ $stock->item->idUrl }}"><strong>{{ $stock->item->name }}</strong></a></div>
        <div><strong>Cost: </strong> {!! $stock->currency->display($stock->cost) !!}</div>
        @if($stock->is_limited_stock) <div>Stock: {{ $stock->quantity }}</div> @endif
        @if($stock->purchase_limit) <div class="text-danger">Max {{ $stock->purchase_limit }} per user</div> @endif
    </div>

    @if($stock->item->parsed_description)
        <div class="mb-2">
            <a data-toggle="collapse" href="#itemDescription" class="h5">Description <i class="fas fa-caret-down"></i></a>
            <div class="card collapse show mt-1" id="itemDescription">
                <div class="card-body">
                    {!! $stock->item->parsed_description !!}
                </div>
            </div>
        </div>
    @endif

    @if(Auth::check())
        <h5>Purchase</h5>
        @if($stock->is_limited_stock && $stock->quantity == 0)
            <div class="alert alert-warning mb-0">This item is out of stock.</div>
        @elseif($purchaseLimitReached)
            <div class="alert alert-warning mb-0">You have already purchased the limit of {{ $stock->purchase_limit }} of this item.</div>
        @else 
            @if($stock->purchase_limit) <div class="alert alert-warning mb-3">You have purchased this item {{ $userPurchaseCount }} times.</div>@endif
            {!! Form::open(['url' => 'shops/buy']) !!}
                {!! Form::hidden('shop_id', $shop->id) !!}
                {!! Form::hidden('stock_id', $stock->id) !!}
                {!! Form::label('quantity', 'Quantity') !!}
                {!! Form::selectRange('quantity', 1, $quantityLimit, 1, ['class' => 'form-control mb-3']) !!}
                @if($stock->use_user_bank && $stock->use_character_bank)
                    <p>This item can be paid for with either your user account bank, or a character's bank. Please choose which you would like to use.</p>
                    <div class="form-group">
                        <div>
                            <label class="h5">{{ Form::radio('bank', 'user' , true, ['class' => 'bank-select mr-1']) }} User Bank</label>
                        </div>
                        <div>
                            <label class="h5">{{ Form::radio('bank', 'character' , false, ['class' => 'bank-select mr-1']) }} Character Bank</label>
                            <div class="card use-character-bank hide">
                                <div class="card-body">
                                    <p>Enter the code of the character you would like to use to purchase the item.</p>
                                    <div class="form-group">
                                        {!! Form::label('slug', 'Character Code') !!}
                                        {!! Form::text('slug', null, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($stock->use_user_bank)
                    <p>This item will be paid for using your user account bank.</p>
                    {!! Form::hidden('bank', 'user') !!}
                @elseif($stock->use_character_bank)
                    <p>This item must be paid for using a character's bank. Enter the code of the character whose bank you would like to use to purchase the item.</p>
                    {!! Form::hidden('bank', 'character') !!}
                    <div class="form-group">
                        {!! Form::label('slug', 'Character Code') !!}
                        {!! Form::text('slug', null, ['class' => 'form-control']) !!}
                    </div>
                @endif
                <div class="text-right">
                    {!! Form::submit('Purchase', ['class' => 'btn btn-primary']) !!}
                </div>
            {!! Form::close() !!}
        @endif
    @else 
        <div class="alert alert-danger">You must be logged in to purchase this item.</div>
    @endif
@endif

@if(Auth::check())
    <script>
        var $useCharacterBank = $('.use-character-bank');
        $('.bank-select').on('click', function(e) {
            if($('input[name=bank]:checked').val() == 'character')
                $useCharacterBank.removeClass('hide');
            else 
                $useCharacterBank.addClass('hide');
        });

    </script>
@endif