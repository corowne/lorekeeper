@extends('user.layout')

@section('profile-title') {{ $user->name }}'s Inventory @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Inventory' => $user->url . '/inventory']) !!}

<h1>
    Inventory
</h1>

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
                            <div class="mb-1">
                                <a href="#" class="inventory-stack"><img src="{{ $stack->first()->imageUrl }}" alt="{{ $stack->first()->name }}" /></a>
                            </div>
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


<h3>Latest Activity</h3>
<div class="mb-4 logs-table">
    <div class="logs-table-header">
        <div class="row">
            <div class="col-6 col-md-2"><div class="logs-table-cell">Sender</div></div>
            <div class="col-6 col-md-2"><div class="logs-table-cell">Recipient</div></div>
            <div class="col-6 col-md-2"><div class="logs-table-cell">Item</div></div>
            <div class="col-6 col-md-4"><div class="logs-table-cell">Log</div></div>
            <div class="col-6 col-md-2"><div class="logs-table-cell">Date</div></div>
        </div>
    </div>
    <div class="logs-table-body">
        @foreach($logs as $log)
            <div class="logs-table-row">
                @include('user._item_log_row', ['log' => $log, 'owner' => $user])
            </div>
        @endforeach
    </div>
</div>
<div class="text-right">
    <a href="{{ url($user->url.'/item-logs') }}">View all...</a>
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
});

</script>
@endsection
