@extends('admin.layout')

@section('admin-title') Loot Tables @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Loot Tables' => 'admin/data/loot-tables']) !!}

<h1>Loot Tables</h1>

<p>Loot tables can be attached to prompts as a reward for doing the prompt. This will roll a random reward from the contents of the table. Tables can be chained as well.</p>

<div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/data/loot-tables/create') }}"><i class="fas fa-plus"></i> Create New Loot Table</a></div>
@if(!count($tables))
    <p>No loot tables found.</p>
@else 
    <table class="table table-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Display Name</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($tables as $table)
                <tr class="sort-item" data-id="{{ $table->id }}">
                    <td>#{{ $table->id }}</td>
                    <td>{{ $table->name }}</td>
                    <td>{{ $table->display_name }}</td>
                    <td class="text-right">
                        <a href="{{ url('admin/data/loot-tables/edit/'.$table->id) }}" class="btn btn-primary">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
@endif

@endsection