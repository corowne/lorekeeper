@extends('home.user_shops.layout')

@section('home.user_shops-title') User Shop Search @endsection

@section('home.user_shops-content')
{!! breadcrumbs(['User Shops' => 'user-shops/shop-index', 'Item Search' => 'user-shops/item-search']) !!}

    <h1>User Shop Item Search</h1>

    <p>Select an item that you are looking to buy from other users, and you will be able to see if any shops are currently stocking it, as well as the cost of each user's items.</p>
    <p>Items that are not currently stocked by any shops will not be shown.</p>
    <p>Selecting a category will limit the search to only items in that category, unless they have been specifically added to the search.</p>

    {!! Form::open(['method' => 'GET', 'class' => '']) !!}
    <div class="form-inline justify-content-end">
        <div class="form-group ml-3 mb-3">
            {!! Form::select('item_ids[]', $items, Request::get('item_ids'), [
                'id' => 'itemList',
                'class' => 'form-control',
                'placeholder' => 'Select Items',
                'style' => 'width: 25em; max-width: 100%;',
                'multiple'
                ])
            !!}
        </div>
        <div class="form-group ml-3 mb-3">
            {!! Form::select('item_category_id', $categories, Request::get('item_category_id'), ['class' => 'form-control', 'placeholder' => 'Search by Category']) !!}
        </div>
        <div class="form-group ml-3 mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
    </div>
    {!! Form::close() !!}

    @if($searched_items)
        <h3>Search Results</h3>
        <p><b>Searching for: </b>{!! $searched_items->pluck('name')->implode(', ') !!}</p>
        @if($category)
            <p>
                <b>Category: </b>{!! $category->displayName !!}
                <br><small>Note that items listed also include items from the chosen category.</small>
            </p>
        @endif
        @if(count($shopItems) && $shopItems->pluck('quantity')->count() > 0)
            <div class="row ml-md-2">
                <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
                    <div class="col col-md-3 font-weight-bold">Item</div>
                    <div class="col col-md-3 font-weight-bold">Shop</div>
                    <div class="col col-md-2 font-weight-bold">Shop Owner</div>
                    <div class="col col-md-2 font-weight-bold">Quantity</div>
                    <div class="col col-md-2 font-weight-bold">Cost</div>
                </div>
                @foreach($shopItems as $itemStock)
                    @php
                        $shop = $itemStock->shop;
                    @endphp
                    <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
                        <div class="col col-md-3">{!! $itemStock->item->displayName !!}</div>
                        <div class="col col-md-3">{!! $shop->displayName !!}</div>
                        <div class="col col-md-2">{!! $shop->user->displayName !!}</div>
                        <div class="col col-md-2">{!! $itemStock->quantity !!}</div>
                        <div class="col col-md-2">{!! $itemStock->cost !!} {!! $itemStock->currency->name !!}</div>
                    </div>
                @endforeach
            </div>
        @else
            No shops are currently stocking the selected items.
        @endif
    @endif

<script>
    $(document).ready(function() {
        $('#itemList').selectize({
            maxItems: 10
        });
    });
</script>

@endsection