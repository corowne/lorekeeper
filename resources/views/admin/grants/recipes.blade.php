@extends('admin.layout')

@section('admin-title') Grant Recipes @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Grant Recipes' => 'admin/grants/recipes']) !!}

<h1>Grant Recipes</h1>

{!! Form::open(['url' => 'admin/grants/recipes']) !!}

<h3>Basic Information</h3>

<div class="form-group">
    {!! Form::label('names[]', 'Username(s)') !!} {!! add_help('You can select up to 10 users at once.') !!}
    {!! Form::select('names[]', $users, null, ['id' => 'usernameList', 'class' => 'form-control', 'multiple']) !!}
</div>

<div class="form-group">
    {!! Form::label('Recipe(s)') !!} {!! add_help('Must have at least 1 recipe.') !!}
    <div id="recipeList">
        <div class="d-flex mb-2">
            {!! Form::select('recipe_ids[]', $recipes, null, ['class' => 'form-control mr-2 default recipe-select', 'placeholder' => 'Select Recipe']) !!}
            <a href="#" class="remove-recipe btn btn-danger mb-2 disabled"><i class="fas fa-times"></i></a>
        </div>
    </div>
    <div><a href="#" class="btn btn-primary" id="add-recipe">Add Recipe</a></div>
    <div class="recipe-row hide mb-2">
        {!! Form::select('recipe_ids[]', $recipes, null, ['class' => 'form-control mr-2 recipe-select', 'placeholder' => 'Select Recipe']) !!}
        <a href="#" class="remove-recipe btn btn-danger mb-2"><i class="fas fa-times"></i></a>
    </div>
</div>

<div class="form-group">
    {!! Form::label('data', 'Reason (Optional)') !!} {!! add_help('A reason for the grant. This will be noted in the logs.') !!}
    {!! Form::text('data', null, ['class' => 'form-control', 'maxlength' => 400]) !!}
</div>

<div class="text-right">
    {!! Form::submit('Submit', ['class' => 'btn btn-primary btn-block']) !!}
</div>

{!! Form::close() !!}

<script>
    $(document).ready(function() {
        $('#usernameList').selectize({
            maxRecipes: 10
        });
        $('.default.recipe-select').selectize();
        $('#add-recipe').on('click', function(e) {
            e.preventDefault();
            addRecipeRow();
        });
        $('.remove-recipe').on('click', function(e) {
            e.preventDefault();
            removeRecipeRow($(this));
        })
        function addRecipeRow() {
            var $rows = $("#recipeList > div")
            if($rows.length === 1) {
                $rows.find('.remove-recipe').removeClass('disabled')
            }
            var $clone = $('.recipe-row').clone();
            $('#recipeList').append($clone);
            $clone.removeClass('hide recipe-row');
            $clone.addClass('d-flex');
            $clone.find('.remove-recipe').on('click', function(e) {
                e.preventDefault();
                removeRecipeRow($(this));
            })
            $clone.find('.recipe-select').selectize();
        }
        function removeRecipeRow($trigger) {
            $trigger.parent().remove();
            var $rows = $("#recipeList > div")
            if($rows.length === 1) {
                $rows.find('.remove-recipe').addClass('disabled')
            }
        }
    });

</script>

@endsection