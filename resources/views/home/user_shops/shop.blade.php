@extends('home.layout')

@section('home-title') Shop Index @endsection

@section('home-content')
{!! breadcrumbs(['Home' => 'home']) !!}

<h1>
   {{ $shop->name }} <a href="{{ url('reports/new?url=') . $shop->url }}"><i class="fas fa-exclamation-triangle fa-xs" data-toggle="tooltip" title="Click here to report this shop." style="opacity: 50%; font-size:0.5em;"></i></a>
</h1>
<div class="mb-3">
    Owned by {!! $shop->user->displayName !!}
</div>

<div class="text-center">
    <img src="{{ $shop->shopImageUrl }}" style="max-width:100%" alt="{{ $shop->name }}" />
    <p>{!! $shop->parsed_description !!}</p>
</div>
@if(count($items))
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
    </div>
@endforeach
@else
<div class="alert alert-secondary text-center mb-3">
    This shop currently has no stock.
</div>
@endif

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.inventory-item').on('click', function(e) {
            e.preventDefault();

            loadModal("{{ url('usershops/'.$shop->id) }}/" + $(this).data('id'), 'Purchase Item');
        });
    });

</script>
@endsection
