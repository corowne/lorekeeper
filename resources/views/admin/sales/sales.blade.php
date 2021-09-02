@extends('admin.layout')

@section('admin-title') Sales @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Sales' => 'admin/sales']) !!}

<h1>Sales</h1>

<p>You can create new sales posts here. Creating a sales post alerts every user that there is a new post, unless the post is marked as not viewable (see the post creation page for details).</p>

<div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/sales/create') }}"><i class="fas fa-plus"></i> Create New Sales Post</a></div>
@if(!count($saleses))
    <p>No sales found.</p>
@else 
    {!! $saleses->render() !!}
    <table class="table table-sm page-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Posted At</th>
                <th>Last Edited</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($saleses as $sales)
                <tr>
                    <td>
                        @if(!$sales->is_visible)
                            @if($sales->post_at)
                                <i class="fas fa-clock" data-toggle="tooltip" title="This post is scheduled to be posted in the future."></i>
                            @else 
                                <i class="fas fa-eye-slash" data-toggle="tooltip" title="This post is hidden."></i>
                            @endif
                        @endif
                        <a href="{{ $sales->url }}">{{ $sales->title }}</a>
                    </td>
                    <td>{!! format_date($sales->post_at ? : $sales->created_at) !!}</td>
                    <td>{!! format_date($sales->updated_at) !!}</td>
                    <td class="text-right">
                        <a href="{{ url('admin/sales/edit/'.$sales->id) }}" class="btn btn-primary">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
    {!! $saleses->render() !!}
@endif

@endsection