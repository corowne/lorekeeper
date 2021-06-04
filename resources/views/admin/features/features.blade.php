@extends('admin.layout')

@section('admin-title') Traits @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Traits' => 'admin/data/traits']) !!}

<h1>Traits</h1>

<p>This is a list of traits that can be attached to characters. </p> 

<div class="text-right mb-3">
    <a class="btn btn-primary" href="{{ url('admin/data/trait-categories') }}"><i class="fas fa-folder"></i> Trait Categories</a>
    <a class="btn btn-primary" href="{{ url('admin/data/traits/create') }}"><i class="fas fa-plus"></i> Create New Trait</a>
</div>

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
    <table class="table table-sm category-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Rarity</th>
                <th>Category</th>
                <th>Species</th>
                <th>Subtype</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($features as $feature)
                <tr class="sort-item" data-id="{{ $feature->id }}">
                    <td>
                        {{ $feature->name }}
                    </td>
                    <td>{!! $feature->rarity->displayName !!}</td>
                    <td>{{ $feature->category ? $feature->category->name : '' }}</td>
                    <td>{{ $feature->species ? $feature->species->name : '' }}</td>
                    <td>{{ $feature->subtype ? $feature->subtype->name : '' }}</td>
                    <td class="text-right">
                        <a href="{{ url('admin/data/traits/edit/'.$feature->id) }}" class="btn btn-primary">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {!! $features->render() !!}
@endif

@endsection

@section('scripts')
@parent
@endsection