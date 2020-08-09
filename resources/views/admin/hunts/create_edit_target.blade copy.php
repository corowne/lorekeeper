@extends('admin.layout')

@section('admin-title') {{ $target->id ? 'Edit' : 'Create' }} Hunt Target @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Scavenger Hunts' => 'admin/data/hunts', 'Edit Scavenger Hunt' => 'admin/data/hunts/edit/'.$hunt->id, ($target->id ? 'Edit' : 'Create').' Target' => $target->id ? 'admin/data/hunts/targets/edit/'.$target->id : 'admin/data/hunts/targets/create/'.$hunt->id]) !!}

<h1>{{ $target->id ? 'Edit' : 'Create' }} Target
    @if($target->id)
        <a href="#" class="btn btn-danger float-right delete-target-button">Delete Target</a>
    @endif
</h1>

<p>Add an item to serve as a hunt target. While targets are identified internally and for admin purposes by their plain ID, they are only identified to members via a random string. This string is generated on creation-- links for placement around the site, etc. will be displayed once the target is created.</p> 

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

<script>
    $(document).ready(function() {
        $('.default.item-select').selectize();
    });
</script>

@endsection