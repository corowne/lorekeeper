@extends('admin.layout')

@section('admin-title') Items @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Items' => 'admin/data/items']) !!}

<h1>Items</h1>

<p>This is a list of items in the game. Specific details about items can be added when they are granted to users (e.g. reason for grant). By default, items are merely collectibles and any additional functionality must be manually processed, or custom coded in for the specific item.</p>

<div class="text-right mb-3">
    <a class="btn btn-primary" href="{{ url('admin/data/item-categories') }}"><i class="fas fa-folder"></i> Item Categories</a>
    <a class="btn btn-primary" href="{{ url('admin/data/items/create') }}"><i class="fas fa-plus"></i> Create New Item</a>
</div>

<div>
    {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Name']) !!}
        </div>
        <div class="form-group mr-3 mb-3">
            {!! Form::select('item_category_id', $categories, Request::get('name'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
</div>

@if(!count($items))
    <p>No items found.</p>
@else
    {!! $items->render() !!}

        <div class="row ml-md-2 mb-4">
          <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
            <div class="col-5 col-md-6 font-weight-bold">Name</div>
            <div class="col-5 col-md-5 font-weight-bold">Category</div>
          </div>
          @foreach($items as $item)
          <div class="d-flex row flex-wrap col-12 mt-1 pt-2 px-0 ubt-top">
            <div class="col-5 col-md-6"> {{ $item->name }} </div>
            <div class="col-4 col-md-5"> {{ $item->category ? $item->category->name : '' }} </div>
            <div class="col-3 col-md-1 text-right">
              <a href="{{ url('admin/data/items/edit/'.$item->id) }}"  class="btn btn-primary py-0 px-2">Edit</a>
            </div>
          </div>
          @endforeach
        </div>

    {!! $items->render() !!}
@endif

@endsection

@section('scripts')
@parent
@endsection
