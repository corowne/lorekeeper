@extends('admin.layout')

@section('admin-title') {{ $target->id ? 'Edit' : 'Create' }} Hunt Target @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Scavenger Hunts' => 'admin/data/hunts', 'Edit Scavenger Hunt' => 'admin/data/hunts/edit/'.$hunt->id, ($target->id ? 'Edit' : 'Create').' Target' => $target->id ? 'admin/data/hunts/targets/edit/'.$target->id : 'admin/data/hunts/targets/create/'.$hunt->id]) !!}

<h1>{{ $target->id ? 'Edit' : 'Create' }} Target
    @if($target->id)
        <a href="#" class="btn btn-danger float-right delete-target-button">Delete Target</a>
    @endif
</h1>

@if($target->id)
    <p>This item serves as the hunt target, and is granted to users upon claiming it. While targets are identified internally and for admin purposes by their plain ID, they are only identified to members via a random string.</p>
@else
    <p>Add an item to serve as a hunt target and be granted to users when they claim the target. While targets are identified internally and for admin purposes by their plain ID, they are only identified to members via a random string. This string is generated on creation-- links for placement around the site, etc. will be displayed once the target is created.</p>
@endif

{!! Form::open(['url' => $target->id ? 'admin/data/hunts/targets/edit/'.$target->id : 'admin/data/hunts/targets/create']) !!}

<div class="form-group">
    {!! Form::label('Item') !!} {!! add_help('Quantity must be at least 1.') !!}
    <div class="row">
        <div class="col-md-6">
            {!! Form::select('item_id', $items, $target->item_id, ['class' => 'form-control mr-2 default item-select', 'placeholder' => 'Select Item']) !!}
        </div>
        <div class="col-md-6">
            {!! Form::text('quantity', $target-> id ? $target->quantity : 1, ['class' => 'form-control mr-2', 'placeholder' => 'Quantity']) !!}
        </div>
    </div>
</div>

@if(!$target->id)
<div class="form-group hide">
    {!! Form::text('hunt_id', $hunt->id, ['class' => 'form-control', 'maxLength' => 250]) !!}
</div>
@endif

<div class="form-group">
    {!! Form::label('Description (Optional)') !!} {!! add_help('You can provide short description, such as a clue, here.') !!}
    {!! Form::text('description', $target->description, ['class' => 'form-control', 'maxLength' => 250]) !!}
</div>

<div class="text-right">
    {!! Form::submit($target->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

@if($target->id)
    <h3>Display Link</h3>
    <p>For convenience, here are links for placing targets around the site, etc. These links, being user-facing, make use of the randomized page ID for targets rather than the orderly (and thus predictable) internal ID. Links will need to be added to their respective destinations via source editing. Feel free to adjust the presentation of them, however. If the target item has an image, it displays only that; if it does not, it displays the item's name.</p>
    <p>HTML</b> {!! add_help('For use around the site, etc.') !!}
        <div class="alert alert-secondary">
            {{ $target->displayLink }}
        </div>
    <p>Wiki</b>
        <div class="alert alert-secondary">
            {{ $target->wikiLink }}
        </div>
@endif

@endsection

@section('scripts')
@parent
<script>
$( document ).ready(function() {    
    $('.delete-target-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/data/hunts/targets/delete') }}/{{ $target->id }}", 'Delete Target');
    });
    
    $(document).ready(function() {
        $('.default.item-select').selectize();
    });
});
    
</script>
@endsection