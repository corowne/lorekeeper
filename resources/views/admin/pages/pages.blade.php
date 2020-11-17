@extends('admin.layout')

@section('admin-title') Pages @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Pages' => 'admin/pages']) !!}

<h1>Pages</h1>

<p>Here you can create pages with custom HTML content. By default, these pages are not linked to by any other page - if you would like users to look at the pages, you will need to link them manually (e.g. in the top navigation, footer, etc.). Certain important pages such as the terms of service and privacy policy cannot be deleted. You can, however, edit their names and visibility.</p>

<div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/pages/create') }}"><i class="fas fa-plus"></i> Create New Page</a></div>
@if(!count($pages))
    <p>No pages found.</p>
@else
    {!! $pages->render() !!}
      <div class="row ml-md-2">
        <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
          <div class="col-12 col-md-5 font-weight-bold">Title</div>
          <div class="col-3 col-md-3 font-weight-bold">Key</div>
          <div class="col-6 col-md-3 font-weight-bold">Last Edited</div>
        </div>
        @foreach($pages as $page)
        <div class="d-flex row flex-wrap col-12 mt-1 pt-2 px-0 ubt-top">
          <div class="col-12 col-md-5"><a href="{{ $page->url }}">{{ $page->title }}</a></div>
          <div class="col-3 col-md-3">{{ $page->key }}</div>
          <div class="col-6 col-md-3">{!! pretty_date($page->updated_at) !!}</div>
          <div class="col-3 col-md-1 text-right"><a href="{{ url('admin/pages/edit/'.$page->id) }}" class="btn btn-primary py-0 px-2">Edit</a></div>
        </div>
        @endforeach
      </div>
    {!! $pages->render() !!}

    <div class="text-center mt-4 small text-muted">{{ $pages->total() }} result{{ $pages->total() == 1 ? '' : 's' }} found.</div>

@endif

@endsection
