@extends('world.layout')

@section('title') Recipes @endsection

@section('content')
{!! breadcrumbs(['World' => 'world', 'Recipes' => 'world/recipes']) !!}
<h1>Recipes</h1>

<div>
    {!! Form::open(['method' => 'GET', 'class' => '']) !!}
        <div class="form-inline justify-content-end">
            <div class="form-group ml-3 mb-3">
                {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Name']) !!}
            </div>
        </div>
        <div class="form-inline justify-content-end">
            <div class="form-group ml-3 mb-3">
                {!! Form::select('sort', [
                    'alpha'          => 'Sort Alphabetically (A-Z)',
                    'alpha-reverse'  => 'Sort Alphabetically (Z-A)',
                    'newest'         => 'Newest First',
                    'oldest'         => 'Oldest First',
                    'locked'         => 'Needs to be Unlocked'
                ], Request::get('sort') ? : 'category', ['class' => 'form-control']) !!}
            </div>
            <div class="form-group ml-3 mb-3">
                {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
            </div>
        </div>
    {!! Form::close() !!}
</div>

{!! $recipes->render() !!}
@foreach($recipes as $recipe)
    <div class="card mb-3">
        <div class="card-body">
       
        @include('world.recipes._recipe_entry', ['recipe' => $recipe, 'imageUrl' => $recipe->imageUrl, 'name' => $recipe->displayName, 'description' => $recipe->parsed_description])
        </div>
    </div>
@endforeach
{!! $recipes->render() !!}

<div class="text-center mt-4 small text-muted">{{ $recipes->total() }} result{{ $recipes->total() == 1 ? '' : 's' }} found.</div>

@endsection
