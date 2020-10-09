@extends('admin.layout')

@section('admin-title') Sub Masterlists @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Sub Masterlists' => 'admin/data/sublists']) !!}

<h1>Sub Masterlists</h1>

<p>Sub masterlists are additional masterlists which can be separate or alternative to the main masterlist. This can be used to divide a masterlist up between species, player versus non-player-character, characters vs pets/mounts, etc.</p>
<p>After creating a sub masterlist, you must select a sublist in either Species or Character Category (or both) for it to apply.</p> 

<div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/data/sublists/create') }}"><i class="fas fa-plus"></i> Create New Sub Masterlist</a></div>

@if(!count($sublists))
    <p>No sub masterlists found.</p>
@else 
    <table class="table table-sm category-table">
        <thead>
            <tr>
                <th>Show on Main</th>
                <th>Name</th>
                <th>Key</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($sublists as $sublist)
                <tr class="sort-item" data-id="{{ $sublist->id }}">
                    <td>{!! $sublist->show_main ? '<i class="text-success fas fa-check"></i>' : '' !!}</td>
                    <td>
                        {{ $sublist->name }}
                    </td>
                    <td>{!! $sublist->key !!}</td>
                    <td class="text-right">
                        <a href="{{ url('admin/data/sublists/edit/'.$sublist->id) }}" class="btn btn-primary">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

@endsection

@section('scripts')
@parent
@endsection