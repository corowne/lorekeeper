
<h2>
    {!! $user->displayName !!}'s Offer
    <span class="float-right">
        @if(Auth::user()->id == $user->id && $trade->status == 'Open')
            @if($trade->{'is_'.$type.'_confirmed'})
                <a href="#" class="btn btn-sm btn-outline-danger" id="confirmOfferButton" data-toggle="tooltip" title="This will unconfirm your offer and allow you to edit it. You will need to reconfirm your offer after you have edited it to proceed.">Unconfirm</a>
            @else
                <a href="{{ url('trades/'.$trade->id.'/edit') }}" class="btn btn-sm btn-primary">Edit</a> <a href="#" class="btn btn-sm btn-outline-primary" id="confirmOfferButton">Confirm</a>
            @endif
        @else
            @if($trade->{'is_'.$type.'_confirmed'})
                @if($trade->{'is_'.$type.'_trade_confirmed'})
                    <small class="text-success">{!! add_help($user->name.' has reviewed your offer and confirmed the trade.') !!} Confirmed</small>
                @else
                    <small class="text-primary">{!! add_help('This offer has been confirmed.') !!} Confirmed</small>
                @endif
            @else
                <small class="text-muted">{!! add_help('This offer has yet to be confirmed.') !!} Pending</small>
            @endif
        @endif
    </span>
</h2>
<div class="card mb-3 trade-offer
        @if($trade->{'is_'.$type.'_confirmed'})
            @if($trade->{'is_'.$type.'_trade_confirmed'})
                border-success
            @else
                border-primary
            @endif
        @endif
">
    @if($data['user_items'])
        <div class="card-header">
            Items
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($data['user_items'] as $item)
                    <div class="col-lg-2 col-sm-3 col-6 mb-3" data-id="{{ $item['asset']->id }}" data-name="{{ $user->name }}'s {{ $item['asset']->item->name }}">
                        <div class="text-center inventory-item">
                            <div class="mb-1">
                                <a class="inventory-stack"><img src="{{ $item['asset']->item->imageUrl }}" /></a>
                            </div>
                            <div>
                                <a class="inventory-stack inventory-stack-name">{{ $item['asset']->item->name }}</a>
                            </div>
                            <div>
                                <a href="#" class="btn btn-xs btn-outline-info inventory-info">Info</a>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    @if($data['characters'])
        <div class="card-header border-top">
            Characters
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($data['characters'] as $character)
                    <div class="col-lg-2 col-sm-3 col-6 mb-3">
                        <div class="text-center inventory-item">
                            <div class="mb-1">
                                <a class="inventory-stack"><img src="{{ $character['asset']->image->thumbnailUrl }}" class="img-thumbnail" /></a>
                            </div>
                            <div>
                                <a class="inventory-stack inventory-stack-name">{!! $character['asset']->displayName !!}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    @if($data['currencies'])
        <div class="card-header border-top border-bottom-0">
            Currencies
        </div>
        <ul class="list-group list-group-flush">
            @foreach($data['currencies'] as $currency)
                <li class="list-group-item border-bottom-0 border-top currency-item">
                    {!! $currency['asset']->display($currency['quantity']) !!}
                </li>
            @endforeach
        </ul>
    @endif
    @if(!$data['user_items'] && !$data['currencies'] && !$data['characters'])
        <div class="card-body">{!! $user->displayName !!} has not added anything to their offer.</div>
    @endif
</div>