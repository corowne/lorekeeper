@extends('admin.layout')

@section('admin-title') Pages @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Pages' => 'admin/pages', ($page->id ? 'Edit' : 'Create').' Page' => $page->id ? 'admin/pages/edit/'.$page->id : 'admin/pages/create']) !!}

<h1>{{ $page->id ? 'Edit' : 'Create' }} Page
    @if($page->id && !Config::get('lorekeeper.text_pages.'.$page->key))
        <a href="#" class="btn btn-danger float-right delete-page-button">Delete Page</a>
    @endif
</h1>

{!! Form::open(['url' => $page->id ? 'admin/pages/edit/'.$page->id : 'admin/pages/create', 'files' => true]) !!}

<h3>Basic Information</h3>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('Title') !!}
            {!! Form::text('title', $page->title, ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('Key') !!} {!! add_help('This is a unique name used to form the URL of the page. Only alphanumeric characters, dash and underscore (no spaces) can be used.') !!}
            {!! Form::text('key', $page->key, ['class' => 'form-control']) !!}
        </div>
    </div>
</div>

<div class="form-group">
    {!! Form::label('Page Content') !!}
    {!! Form::textarea('text', $page->text, ['class' => 'form-control wysiwyg']) !!}
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::checkbox('is_visible', 1, $page->id ? $page->is_visible : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
            {!! Form::label('is_visible', 'Is Viewable', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If this is turned off, users will not be able to view the page even if they have the link to it.') !!}
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            {!! Form::checkbox('can_comment', 1, $page->id ? $page->can_comment : 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
            {!! Form::label('can_comment', 'Commentable', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If this is turned on, users will be able to comment on the page.') !!}
        </div>
    </div>
</div>

<div class="text-right">
    {!! Form::submit($page->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

@endsection

@section('scripts')
@parent
<script>
$( document ).ready(function() {    
    $('.delete-page-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/pages/delete') }}/{{ $page->id }}", 'Delete Page');
    });
});
    
</script>
@endsection