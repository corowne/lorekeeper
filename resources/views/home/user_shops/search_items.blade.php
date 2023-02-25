@extends('home.layout')

@section('home-title') Shop Index @endsection

@section('home-content')
{!! breadcrumbs(['Home' => 'home']) !!}

<h1>Item Search</h1>

<p>Select an item to search for all occurrences of it in shop and character inventories. It will only display currently extant stacks (where the count is more than zero). If a stack is currently "held" in a trade, design update, or submission, this will be stated and all held locations will be linked.</p>

{!! Form::open(['method' => 'GET', 'class' => '']) !!}
<div class="form-inline justify-content-end">
    <div class="form-group ml-3 mb-3">
        {!! Form::select('item_id', $items, Request::get('item_id'), ['class' => 'form-control selectize', 'placeholder' => 'Select an Item', 'style' => 'width: 25em; max-width: 100%;']) !!}
    </div>
    <div class="form-group ml-3 mb-3">
        {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
    </div>
</div>
{!! Form::close() !!}

@if($item)
    <h3>{{ $item->name }}</h3>

    <p>There are currently {{ $shopItems->pluck('count') }} of this item owned by shops and characters.</p>

    <ul>
        @foreach($shops as $shop)
            <li>
                {!! $shop->displayName !!} has {{ $shopItems->where('shop_id', $shop->id)->pluck('count')->sum() }}
                @if($shopItems->where('shop_id', $shop->id)->pluck('count')->sum() > $shopItems->where('shop_id', $shop->id)->pluck('availableQuantity')->sum())
                 ({{ $shopItems->where('shop_id', $shop->id)->pluck('availableQuantity')->sum() }} Available)
                @endif
            </li>
        @endforeach 
    </ul>
@endif

<script>
    $(document).ready(function() {
        $('.selectize').selectize();
    });
</script>

@endsection