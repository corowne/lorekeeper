@extends('admin.layout')

@section('admin-title') Recipes @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Recipes' => 'admin/data/recipes', ($recipe->id ? 'Edit' : 'Create').' Recipe' => $recipe->id ? 'admin/data/recipes/edit/'.$recipe->id : 'admin/data/recipes/create']) !!}

<h1>{{ $recipe->id ? 'Edit' : 'Create' }} Recipe
    @if($recipe->id)
        <a href="#" class="btn btn-outline-danger float-right delete-recipe-button">Delete Recipe</a>
    @endif
</h1>

{!! Form::open(['url' => $recipe->id ? 'admin/data/recipes/edit/'.$recipe->id : 'admin/data/recipes/create', 'files' => true]) !!}

<h3>Basic Information</h3>

<div class="form-group">
    {!! Form::label('Name') !!}
    {!! Form::text('name', $recipe->name, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('World Page Image (Optional)') !!} {!! add_help('This image is used only on the world information pages.') !!}
    <div>{!! Form::file('image') !!}</div>
    <div class="text-muted">Recommended size: 100px x 100px</div>
    @if($recipe->has_image)
        <div class="form-check">
            {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
            {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
        </div>
    @endif
</div>

{!! Form::checkbox('needs_unlocking', 1, false, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
{!! Form::label('needs_unlocking', 'Locked by default', ['class' => 'form-check-label ml-3 mb-3']) !!}

<div class="form-group">
    {!! Form::label('Description (Optional)') !!}
    {!! Form::textarea('description', $recipe->description, ['class' => 'form-control wysiwyg']) !!}
</div>

<h3>Recipe Ingredients</h3>
@include('widgets._recipe_ingredient_select', ['ingredients' => $recipe->ingredients])

<hr>

<h3>Recipe Rewards</h3>
@include('widgets._recipe_reward_select', ['rewards' => $recipe->rewards])

<div class="text-right">
    {!! Form::submit($recipe->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

@include('widgets._recipe_ingredient_select_row', ['items' => $items, 'categories' => $categories, 'currencies' => $currencies])
@include('widgets._recipe_reward_select_row', ['items' => $items, 'currencies' => $currencies, 'tables' => $tables])

@if($recipe->id)
    <h3>Preview</h3>
    <div class="card mb-3">
        <div class="card-body">
            @include('world._entry', ['imageUrl' => $recipe->imageUrl, 'name' => $recipe->displayName, 'description' => $recipe->parsed_description, 'searchUrl' => $recipe->searchUrl])
        </div>
    </div>
@endif

@endsection

@section('scripts')
@parent
@include('js._recipe_reward_js')
@include('js._recipe_ingredient_js')
<script>
$( document ).ready(function() {    
    $('.delete-recipe-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/data/recipes/delete') }}/{{ $recipe->id }}", 'Delete Recipe');
    });
});
    
</script>
@endsection