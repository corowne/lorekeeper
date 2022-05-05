@extends('home.layout')

@section('home-title') Inventory @endsection

@section('home-content')
{!! breadcrumbs(['Inventory' => 'inventory']) !!}

<h1>
    Inventory
    <div class="float-right mb-3">
        <a class="btn btn-secondary consolidate-inventory" href="#">Consolidate</a>
        <a class="btn btn-primary" href="{{ url('inventory/account-search') }}"><i class="fas fa-search"></i> Account Search</a>
    </div>
</h1>

<p>This is your inventory. Click on an item to view more details and actions you can perform on it.</p>

@foreach($items as $categoryId=>$categoryItems)
    <div class="card mb-3 inventory-category">
        <h5 class="card-header inventory-header">
            {!! isset($categories[$categoryId]) ? '<a href="'.$categories[$categoryId]->searchUrl.'">'.$categories[$categoryId]->name.'</a>' : 'Miscellaneous' !!}
            <a class="small inventory-collapse-toggle collapse-toggle collapsed" href="#{!! isset($categories[$categoryId]) ? str_replace(' ', '', $categories[$categoryId]->name) : 'miscellaneous' !!}" data-toggle="collapse">Show</a></h3>
        </h5>
        <div class="card-body inventory-body collapse show" id="{!! isset($categories[$categoryId]) ? str_replace(' ', '', $categories[$categoryId]->name) : 'miscellaneous' !!}">
            @foreach($categoryItems->chunk(4) as $chunk)
                <div class="row mb-3">
                    @foreach($chunk as $itemId=>$stack)
                        <div class="col-sm-3 col-6 text-center inventory-item" data-id="{{ $stack->first()->pivot->id }}" data-name="{{ $user->name }}'s {{ $stack->first()->name }}">
                            @if($stack->first()->has_image)
                                <div class="mb-1">
                                    <a href="#" class="inventory-stack"><img src="{{ $stack->first()->imageUrl }}" alt="{{ $stack->first()->name }}"/></a>
                                </div>
                            @endif
                            <div>
                                <a href="#" class="inventory-stack inventory-stack-name">{{ $stack->first()->name }} x{{ $stack->sum('pivot.count') }}</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
@endforeach
<div class="text-right mb-4">
    <a href="{{ url(Auth::user()->url.'/item-logs') }}">View logs...</a>
</div>

@endsection
@section('scripts')
<script>

$( document ).ready(function() {
    $('.inventory-stack').on('click', function(e) {
        e.preventDefault();
        var $parent = $(this).parent().parent();
        loadModal("{{ url('items') }}/" + $parent.data('id'), $parent.data('name'));
    });
    $('.consolidate-inventory').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('inventory/consolidate-inventory') }}", 'Consolidate Inventory');
    });
});

</script>
@endsection
