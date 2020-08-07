@extends('admin.layout')

@section('admin-title') Scavenger Hunts @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Scavenger Hunts' => 'admin/data/hunts']) !!}

<h1>Scavenger Hunts</h1>

<p>This is a list of scavenger hunts.</p> 

<div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/data/hunts/create') }}"><i class="fas fa-plus"></i> Create New Hunt</a></div>

@if(!count($hunts))
    <p>No hunts found.</p>
@else 
    {!! $hunts->render() !!}
    <table class="table table-sm category-table">
        <thead>
            <tr>
                <th>Active</th>
                <th>Name</th>
                <th>Starts</th>
                <th>Ends</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($hunts as $hunt)
                <tr class="sort-prompt" data-id="{{ $hunt->id }}">
                    <td class="text-center">{!! $hunt->isActive ? '<i class="text-success fas fa-check"></i>' : '' !!}</td>
                    <td>
                        {{ $hunt->name }}
                    </td>
                    <td>
                        {!! $hunt->start_at ? format_date($hunt->start_at) : '' !!}
                    </td>
                    <td>
                        {!! $hunt->end_at ? format_date($hunt->end_at) : '' !!}
                    </td>
                    <td class="text-right">
                        <a href="{{ url('admin/data/prompts/edit/'.$prompt->id) }}" class="btn btn-primary">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {!! $hunts->render() !!}
@endif

@endsection

@section('scripts')
@parent
@endsection