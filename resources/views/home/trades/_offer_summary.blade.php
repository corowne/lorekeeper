@if($data['user_items'])
    <div class="row">
        @foreach($data['user_items'] as $item)
            <div class="col-sm-3 col-4 mb-3" title="{{ $item['asset']->item->name }}" data-toggle="tooltip">
                <div class="text-center inventory-item">
                    <div class="mb-1">
                        <a class="inventory-stack"><img src="{{ $item['asset']->item->imageUrl }}" class="mw-100" /></a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@if($data['characters'])
    <div class="row">
        @foreach($data['characters'] as $character)
            <div class="col-sm-3 col-4 mb-3">
                <div class="text-center inventory-item">
                    <div class="mb-1">
                        <a class="inventory-stack"><img src="{{ $character['asset']->image->thumbnailUrl }}" class="img-thumbnail" title="{{ $character['asset']->fullName }}" data-toggle="tooltip" /></a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@if($data['currencies'])
    <div>
        @foreach($data['currencies'] as $currency)
            <div>
                {!! $currency['asset']->display($currency['quantity']) !!}
            </div>
        @endforeach
    </div>
@endif