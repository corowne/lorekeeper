@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title') {{ $character->fullName }}'s Inventory @endsection

@section('profile-content')
@if($character->is_myo_slot)
{!! breadcrumbs(['MYO Slot Masterlist' => 'myos', $character->fullName => $character->url, 'Inventory' => $character->url.'/inventory']) !!}
@else
{!! breadcrumbs([($character->category->masterlist_sub_id ? $character->category->sublist->name.' Masterlist' : 'Character masterlist') => ($character->category->masterlist_sub_id ? 'sublist/'.$character->category->sublist->key : 'masterlist' ), $character->fullName => $character->url, 'Inventory' => $character->url.'/inventory']) !!}
@endif

@include('character._header', ['character' => $character])

<h3>
    @if(Auth::check() && Auth::user()->hasPower('edit_inventories'))
        <a href="#" class="float-right btn btn-outline-info btn-sm" id="grantButton" data-toggle="modal" data-target="#grantModal"><i class="fas fa-cog"></i> Admin</a>
    @endif
    Items
</h3>

@foreach($items as $categoryId=>$categoryItems)
    <div class="card mb-3 inventory-category">
        <h5 class="card-header inventory-header">
            {!! isset($categories[$categoryId]) ? '<a href="'.$categories[$categoryId]->searchUrl.'">'.$categories[$categoryId]->name.'</a>' : 'Miscellaneous' !!}
        </h5>
        <div class="card-body inventory-body">
            @foreach($categoryItems->chunk(4) as $chunk)
                <div class="row mb-3">
                    @foreach($chunk as $itemId=>$stack)
                        <?php
                            $canName = $stack->first()->category->can_name;
                            $stackName = $stack->first()->pivot->pluck('stack_name', 'id')->toArray()[$stack->first()->pivot->id];
                            $stackNameClean = htmlentities($stackName);
                        ?>
                        <div class="col-sm-3 col-6 text-center inventory-item" data-id="{{ $stack->first()->pivot->id }}" data-name="{!! $canName && $stackName ? htmlentities($stackNameClean).' [' : null !!}{{ $character->name ? $character->name : $character->slug }}'s {{ $stack->first()->name }}{!! $canName && $stackName ? ']' : null !!}">
                            <div class="mb-1">
                                <a href="#" class="inventory-stack"><img src="{{ $stack->first()->imageUrl }}" alt="{{ $stack->first()->name }}"/></a>
                            </div>
                            <div class="{{ $canName ? 'text-muted' : '' }}">
                                <a href="#" class="inventory-stack inventory-stack-name">{{ $stack->first()->name }} x{{ $stack->sum('pivot.count') }}</a>
                            </div>
                            @if($canName && $stackName)
                                <div>
                                    <span class="inventory-stack inventory-stack-name badge badge-info" style="font-size:95%; margin:5px;">"{{ $stackName }}"</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
@endforeach

<h3>Latest Activity</h3>
<div class="row ml-md-2 mb-4">
  <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-bottom">
    <div class="col-6 col-md-2 font-weight-bold">Sender</div>
    <div class="col-6 col-md-2 font-weight-bold">Recipient</div>
    <div class="col-6 col-md-2 font-weight-bold">Character</div>
    <div class="col-6 col-md-4 font-weight-bold">Log</div>
    <div class="col-6 col-md-2 font-weight-bold">Date</div>
  </div>
      @foreach($logs as $log)
          @include('user._item_log_row', ['log' => $log, 'owner' => $character])
      @endforeach
</div>
<div class="text-right">
    <a href="{{ url($character->url.'/item-logs') }}">View all...</a>
</div>

@if(Auth::check() && Auth::user()->hasPower('edit_inventories'))
    <div class="modal fade" id="grantModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="modal-title h5 mb-0">[ADMIN] Grant Items</span>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                <p>Note that granting items does not check against any category hold limits for characters.</p>
                <div class="form-group">
                {!! Form::open(['url' => 'admin/character/'.$character->slug.'/grant-items']) !!}

                    {!! Form::label('Item(s)') !!} {!! add_help('Must have at least 1 item and Quantity must be at least 1.') !!}
                    <div id="itemList">
                        <div class="d-flex mb-2">
                            {!! Form::select('item_ids[]', $itemOptions, null, ['class' => 'form-control mr-2 default item-select', 'placeholder' => 'Select Item']) !!}
                            {!! Form::text('quantities[]', 1, ['class' => 'form-control mr-2', 'placeholder' => 'Quantity']) !!}
                            <a href="#" class="remove-item btn btn-danger mb-2 disabled">×</a>
                        </div>
                    </div>
                    <div><a href="#" class="btn btn-primary" id="add-item">Add Item</a></div>
                    <div class="item-row hide mb-2">
                        {!! Form::select('item_ids[]', $itemOptions, null, ['class' => 'form-control mr-2 item-select', 'placeholder' => 'Select Item']) !!}
                        {!! Form::text('quantities[]', 1, ['class' => 'form-control mr-2', 'placeholder' => 'Quantity']) !!}
                        <a href="#" class="remove-item btn btn-danger mb-2">×</a>
                    </div>

                    <h5>Additional Data</h5>

                    <div class="form-group">
                        {!! Form::label('data', 'Reason (Optional)') !!} {!! add_help('A reason for the grant. This will be noted in the logs and in the inventory description.') !!}
                        {!! Form::text('data', null, ['class' => 'form-control', 'maxlength' => 400]) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('notes', 'Notes (Optional)') !!} {!! add_help('Additional notes for the item. This will appear in the item\'s description, but not in the logs.') !!}
                        {!! Form::text('notes', null, ['class' => 'form-control', 'maxlength' => 400]) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::checkbox('disallow_transfer', 1, 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                        {!! Form::label('disallow_transfer', 'Character-bound', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If this is on, the character\'s owner will not be able to transfer this item to their inventory. Items that disallow transfers by default will still not be transferrable.') !!}
                    </div>

                    <div class="text-right">
                        {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
                    </div>

                {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endif

@endsection

@section('scripts')

@include('widgets._inventory_select_js', ['readOnly' => true])

<script>

$( document ).ready(function() {
    $('.inventory-stack').on('click', function(e) {
        e.preventDefault();
        var $parent = $(this).parent().parent();
        loadModal("{{ url('items') }}/character/" + $parent.data('id'), $parent.data('name'));
    });

    $('.default.item-select').selectize();
        $('#add-item').on('click', function(e) {
            e.preventDefault();
            addItemRow();
        });
        $('.remove-item').on('click', function(e) {
            e.preventDefault();
            removeItemRow($(this));
        })
        function addItemRow() {
            var $rows = $("#itemList > div")
            if($rows.length === 1) {
                $rows.find('.remove-item').removeClass('disabled')
            }
            var $clone = $('.item-row').clone();
            $('#itemList').append($clone);
            $clone.removeClass('hide item-row');
            $clone.addClass('d-flex');
            $clone.find('.remove-item').on('click', function(e) {
                e.preventDefault();
                removeItemRow($(this));
            })
            $clone.find('.item-select').selectize();
        }
        function removeItemRow($trigger) {
            $trigger.parent().remove();
            var $rows = $("#itemList > div")
            if($rows.length === 1) {
                $rows.find('.remove-item').addClass('disabled')
            }
        }
});

</script>
@endsection
