@extends('admin.layout')

@section('admin-title') Pages @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Pages' => 'admin/pages']) !!}

<h1>Pages</h1>

<p>Here you can create pages with custom HTML content. By default, these pages are not linked to by any other page - if you would like users to look at the pages, you will need to link them manually (e.g. in the top navigation, footer, etc.). Certain important pages such as the terms of service and privacy policy cannot be deleted. You can, however, edit their names and visibility.</p>

<div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/pages/create') }}"><i class="fas fa-plus"></i> Create New Page</a></div>

<div>
    {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Title']) !!}
        </div>
        <div class="form-group mr-3 mb-3">
            {!! Form::select('page_category_id', $categories, Request::get('name'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
</div>

@if(!count($pages))
    <p>No pages found.</p>
@else 
    {!! $pages->render() !!}
    <table class="table table-sm page-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Key</th>
                <th>Last Edited</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($pages as $page)
                <tr>
                    <td>
                        <a href="{{ $page->url }}">{{ $page->title }}</a>
                    </td>
                    <td>{{ $page->category ? $page->category->name : '' }}</td>
                    <td>{{ $page->key }}</td>
                    <td>{!! format_date($page->updated_at) !!}</td>
                    <td class="text-right">
                        <a href="{{ url('admin/pages/edit/'.$page->id) }}" class="btn btn-primary">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
    {!! $pages->render() !!}
    <div class="text-center mt-4 small text-muted">{{ $pages->total() }} result{{ $pages->total() == 1 ? '' : 's' }} found.</div>
@endif

@endsection