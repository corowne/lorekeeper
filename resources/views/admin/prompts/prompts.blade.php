@extends('admin.layout')

@section('admin-title') Prompts @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Prompts' => 'admin/data/prompts']) !!}

<h1>Prompts</h1>

<p>This is a list of prompts users can submit to.</p> 

<div class="text-right mb-3">
    <a class="btn btn-primary" href="{{ url('admin/data/prompt-categories') }}"><i class="fas fa-folder"></i> Prompt Categories</a>
    <a class="btn btn-primary" href="{{ url('admin/data/prompts/create') }}"><i class="fas fa-plus"></i> Create New Prompt</a>
</div>

<div>
    {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Name']) !!}
        </div>
        <div class="form-group mr-3 mb-3">
            {!! Form::select('prompt_category_id', $categories, Request::get('name'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
</div>

@if(!count($prompts))
    <p>No prompts found.</p>
@else 
    {!! $prompts->render() !!}
    <table class="table table-sm category-table">
        <thead>
            <tr>
                <th>Active</th>
                <th>Name</th>
                <th>Category</th>
                <th>Starts</th>
                <th>Ends</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($prompts as $prompt)
                <tr class="sort-prompt" data-id="{{ $prompt->id }}">
                    <td class="text-center">{!! $prompt->is_active ? '<i class="text-success fas fa-check"></i>' : '' !!}</td>
                    <td>
                        {{ $prompt->name }}
                    </td>
                    <td>{{ $prompt->category ? $prompt->category->name : '' }}</td>
                    <td>
                        {!! $prompt->start_at ? format_date($prompt->start_at) : '' !!}
                    </td>
                    <td>
                        {!! $prompt->end_at ? format_date($prompt->end_at) : '' !!}
                    </td>
                    <td class="text-right">
                        <a href="{{ url('admin/data/prompts/edit/'.$prompt->id) }}" class="btn btn-primary">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {!! $prompts->render() !!}
@endif

@endsection

@section('scripts')
@parent
@endsection