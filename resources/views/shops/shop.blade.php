@extends('shops.layout')

@section('shops-title') {{ $shop->name }} @endsection

@section('shops-content')
{!! breadcrumbs(['Shops' => 'shops', $shop->name => $shop->url]) !!}

<h1>
    {{ $shop->name }}
</h1>

<div class="text-center">
    <img src="{{ $shop->shopImageUrl }}" style="max-width:100%" />
    <p>{!! $shop->parsed_description !!}</p>
</div>

@foreach($items as $categoryId=>$categoryItems)
    <div class="card mb-3 inventory-category">
        <h5 class="card-header inventory-header">
            {!! isset($categories[$categoryId]) ? '<a href="'.$categories[$categoryId]->searchUrl.'">'.$categories[$categoryId]->name.'</a>' : 'Miscellaneous' !!}
        </h5>
        <div class="card-body inventory-body">
            @foreach($categoryItems->chunk(4) as $chunk)
                <div class="row mb-3">
                    @foreach($chunk as $item) 
                        <div class="col-sm-3 col-6 text-center inventory-item" data-id="{{ $item->pivot->id }}">
                            <div class="mb-1">
                                <a href="#" class="inventory-stack"><img src="{{ $item->imageUrl }}" /></a>
                            </div>
                            <div>
                                <a href="#" class="inventory-stack inventory-stack-name"><strong>{{ $item->name }}</strong></a>
                                <div><strong>Cost: </strong> {!! $currencies[$item->pivot->currency_id]->display($item->pivot->cost) !!}</div>
                                @if($item->pivot->is_limited_stock) <div>Stock: {{ $item->pivot->quantity }}</div> @endif
                                @if($item->pivot->purchase_limit) <div class="text-danger">Max {{ $item->pivot->purchase_limit }} per user</div> @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
@endforeach

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.inventory-item').on('click', function(e) {
            e.preventDefault();
            
            loadModal("{{ url('shops/'.$shop->id) }}/" + $(this).data('id'), 'Purchase Item');
        });
    });

</script>
@endsection