@extends('admin.layout')

@section('admin-title') Grant Items @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Grant Items' => 'admin/grants/items']) !!}

<h1>Grant Items</h1>

{!! Form::open(['url' => 'admin/grants/items']) !!}

<h3>Basic Information</h3>

<div class="form-group">
    {!! Form::label('names', 'Username(s)') !!} {!! add_help('You can list multiple users as a comma-separated list, e.g. "user1, user2, user3"') !!}
    {!! Form::text('names', null, ['class' => 'form-control']) !!}
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('item_id', 'Item') !!} 
            {!! Form::select('item_id', $items, null, ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('quantity', 'Quantity') !!} {!! add_help('This must be at least 1.') !!}
            {!! Form::text('quantity', 1, ['class' => 'form-control']) !!}
        </div>
    </div>
</div>

<div class="form-group">
    {!! Form::label('data', 'Reason (Optional)') !!} {!! add_help('A reason for the grant. This will be noted in the logs and in the inventory description.') !!}
    {!! Form::text('data', null, ['class' => 'form-control', 'maxlength' => 400]) !!}
</div>

<h3>Additional Data</h3>

<div class="form-group">
    {!! Form::label('notes', 'Notes (Optional)') !!} {!! add_help('Additional notes for the item. This will appear in the item\'s description, but not in the logs.') !!}
    {!! Form::text('notes', null, ['class' => 'form-control', 'maxlength' => 400]) !!}
</div>

<div class="form-group">
    {!! Form::checkbox('disallow_transfer', 1, 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
    {!! Form::label('disallow_transfer', 'Account-bound', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If this is on, the recipient(s) will not be able to transfer this item to other users. Items that disallow transfers by default will still not be transferrable.') !!}
</div>

<div class="text-right">
    {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

@endsection