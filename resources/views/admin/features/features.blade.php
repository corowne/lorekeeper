@extends('admin.layout')

@section('admin-title') Traits @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Traits' => 'admin/data/traits']) !!}

<h1>Traits</h1>

<p>This is a list of traits that can be attached to characters. </p>

<div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/data/traits/create') }}"><i class="fas fa-plus"></i> Create New Trait</a></div>

<div>
    {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Name']) !!}
        </div>
        <div class="form-group mr-3 mb-3">
            {!! Form::select('species_id', $specieses, Request::get('species_id'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mr-3 mb-3">
            {!! Form::select('subtype_id', $subtypes, Request::get('subtype_id'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mr-3 mb-3">
            {!! Form::select('rarity_id', $rarities, Request::get('rarity_id'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mr-3 mb-3">
            {!! Form::select('feature_category_id', $categories, Request::get('name'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
</div>

@if(!count($features))
    <p>No traits found.</p>
@else
    {!! $features->render() !!}
      <div class="row ml-md-2">
        <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
          <div class="col-12 col-md-3 font-weight-bold">Name</div>
          <div class="col-6 col-md-2 font-weight-bold">Rarity</div>
          <div class="col-6 col-md-2 font-weight-bold">Category</div>
          <div class="col-6 col-md-2 font-weight-bold">Species</div>
          <div class="col-6 col-md-2 font-weight-bold">Subtype</div>
        </div>
        @foreach($features as $feature)
        <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
          <div class="col-12 col-md-3">{{ $feature->name }}</div>
          <div class="col-6 col-md-2">{!! $feature->rarity->displayName !!}</div>
          <div class="col-6 col-md-2">{{ $feature->category ? $feature->category->name : '---' }}</div>
          <div class="col-6 col-md-2">{{ $feature->species ? $feature->species->name : '---' }}</div>
          <div class="col-6 col-md-2">{{ $feature->subtype ? $feature->subtype->name : '---' }}</div>
          <div class="col-12 col-md-1"><a href="{{ url('admin/data/traits/edit/'.$feature->id) }}" class="btn btn-primary py-0 px-1 w-100">Edit</a></div>
        </div>
        @endforeach
      </div>
    {!! $features->render() !!}
    <div class="text-center mt-4 small text-muted">{{ $features->total() }} result{{ $features->total() == 1 ? '' : 's' }} found.</div>
@endif

@endsection

@section('scripts')
@parent
@endsection
