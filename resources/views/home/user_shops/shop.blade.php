@extends('home.user_shops.layout')

@section('home.user_shops-title') Shop Index @endsection

@section('home.user_shops-content')
{!! breadcrumbs(['User Shops' => 'user-shops/shop-index', $shop->name => 'user-shops/shop/1']) !!}

@if(Auth::check() && Auth::user()->id === $shop->user_id || Auth::user()->hasPower('edit_inventories'))
    <a data-toggle="tooltip" title="Edit Shop" href="{{ url('user-shops/edit').'/'.$shop->id }}" class="mb-2 float-right"><h3><i class="fas fa-pencil-alt"></i></h3></a>
@endif

<h1>
   {{ $shop->name }} <a href="{{ url('reports/new?url=') . $shop->url }}"><i class="fas fa-exclamation-triangle fa-xs" data-toggle="tooltip" title="Click here to report this shop." style="opacity: 50%; font-size:0.5em;"></i></a>
</h1>
<div class="mb-3">
    Owned by {!! $shop->user->displayName !!}
</div>

<div class="text-center">
    @if($shop->shopImageUrl)
        <img src="{{ $shop->shopImageUrl }}" style="max-width:50%;" alt="{{ $shop->name }}"/>
    @endif
    <p>{!! $shop->parsed_description !!}</p>
</div>
@if(count($items))
<h3> Items <a class="small inventory-collapse-toggle collapse-toggle collapsed" href="#itemstockcollapsible" data-toggle="collapse">Collapse View</a></h3>
<div class="card mb-3 inventory-category collapse show" id="itemstockcollapsible">
            <div class="card-body inventory-body">
                <div class="mb-3">
        <ul class="nav nav-tabs card-header-tabs">
            @foreach($items as $categoryId=>$categoryItems)
                <li class="nav-item">
                    <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="categoryTab-{{ isset($categories[$categoryId]) ? $categoryId : 'misc'}}" data-toggle="tab" href="#category-{{ isset($categories[$categoryId]) ? $categoryId : 'misc'}}" role="tab">
                        {!! isset($categories[$categoryId]) ? $categories[$categoryId]->name : 'Miscellaneous' !!}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
    <div class="card-body tab-content">
        @foreach($items as $categoryId=>$categoryItems)
            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="category-{{ isset($categories[$categoryId]) ? $categoryId : 'misc'}}">
                @foreach($categoryItems->chunk(4) as $chunk)
                    <div class="row mb-3">
                        @foreach($chunk as $item)
                        <div class="col-sm-3 col-6 text-center inventory-item" data-id="{{ $item->pivot->id }}">
                            <div class="mb-1">
                                <a href="#" class="inventory-stack"><img src="{{ $item->imageUrl }}" alt="{{ $item->name }}" /></a>
                            </div>
                            <div>
                                <a href="#" class="inventory-stack inventory-stack-name"><strong>{{ $item->name }}</strong></a>
                                <div><strong>Cost: </strong> {!! $currencies[$item->pivot->currency_id]->display($item->pivot->cost) !!}</div> 
                                <div>Stock: {{ $item->pivot->quantity }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>
</div>
@endif

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.inventory-item').on('click', function(e) {
            e.preventDefault();

            loadModal("{{ url('user-shops/'.$shop->id) }}/" + $(this).data('id'), 'Purchase Item');
        });
    });

</script>
@endsection
