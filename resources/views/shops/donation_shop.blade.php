@extends('shops.layout')

@section('shops-title') Donation Shop @endsection

@section('shops-content')
{!! breadcrumbs(['Shops' => 'shops', 'Donation Shop' => 'shops/donation-shop']) !!}

<h1>
    Donation Shop
</h1>

<div class="text-center">
    <img src="{{ asset('images/donation_shop.png') }}" style="max-width:100%" />
    <p>{!! $text->parsed_text !!}</p>
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
                        <div class="col-sm-3 col-6 text-center inventory-item" data-id="{{ $item->id }}">
                            <div class="mb-1">
                                <a href="#" class="inventory-stack"><img src="{{ $item->item->imageUrl }}" /></a>
                            </div>
                            <div>
                                <a href="#" class="inventory-stack inventory-stack-name"><strong>{{ $item->item->name }}</strong></a>
                                <div>Stock: {{ $item->stock }}</div>
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

            loadModal("{{ url('shops/donation-shop') }}/" + $(this).data('id'), 'Collect Item');
        });
    });

</script>
@endsection
