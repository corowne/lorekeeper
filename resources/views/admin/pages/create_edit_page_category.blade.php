@extends('admin.layout')

@section('admin-title') Page Categories @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Page Categories' => 'admin/page-categories', ($category->id ? 'Edit' : 'Create').' Category' => $category->id ? 'admin/page-categories/edit/'.$category->id : 'admin/page-categories/create']) !!}

<h1>{{ $category->id ? 'Edit' : 'Create' }} Category
    @if($category->id)
        <a href="#" class="btn btn-danger float-right delete-category-button">Delete Category</a>
    @endif
</h1>

{!! Form::open(['url' => $category->id ? 'admin/page-categories/edit/'.$category->id : 'admin/page-categories/create', 'files' => true]) !!}

<h3>Basic Information</h3>

<div class="form-group">
    {!! Form::label('Name') !!}
    {!! Form::text('name', $category->name, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('Page Section (Optional)') !!} {!! add_help('A Page Section is a group listed in the Encyclopedia. You need a Section to list a category for display, otherwise it will remain unlisted.') !!}
    {!! Form::select('section_id', $sections, $category->section_id, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('World Page Image (Optional)') !!} {!! add_help('This image is used only on the world information pages.') !!}
    <div>{!! Form::file('image') !!}</div>
    <div class="text-muted">Recommended size: 200px x 200px</div>
    @if($category->has_image)
        <div class="form-check">
            {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
            {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
        </div>
    @endif
</div>

<div class="form-group">
    {!! Form::label('Description (Optional)') !!}
    {!! Form::textarea('description', $category->description, ['class' => 'form-control wysiwyg']) !!}
</div>

<div class="text-right">
    {!! Form::submit($category->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

@if($category->id)
    <h3>Preview</h3>
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header bg-transparent text-center pb-0">
                @if($category->categoryImageUrl)
                    <div class="world-entry-image"><a href="{{ $category->categoryImageUrl }}" data-lightbox="entry" data-title="{{ $category->name }}">
                    <img class="img-fluid" src="{{ $category->categoryImageUrl }}" class="world-entry-image" /></a></div>
                @endif
                <h5 class="card-title">{!! $category->name !!}</h5>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item text-center">
                <p class=card-text>{!! $category->description !!}</p>
                </li>
                <li class="list-group-item">
                <p class=card-text>
                <a href="">Active Page Link</a>
                </p>
                </li>
                <li class="list-group-item">
                <p class=card-text>
                <span class="text-muted">Invisible Page Link</span>
                </p>
                </li>
            </ul>
        </div>
    </div>
@endif

@endsection

@section('scripts')
@parent
<script>
$( document ).ready(function() {    
    $('.delete-category-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/page-categories/delete') }}/{{ $category->id }}", 'Delete Category');
    });
});
    
</script>
@endsection