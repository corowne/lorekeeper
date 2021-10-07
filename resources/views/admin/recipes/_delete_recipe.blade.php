@if($recipe)
    {!! Form::open(['url' => 'admin/data/recipes/delete/'.$recipe->id]) !!}

    <p>You are about to delete the recipe <strong>{{ $recipe->name }}</strong>. This is not reversible. If this recipe exists in at least one user's possession, you will not be able to delete this recipe.</p>
    <p>Are you sure you want to delete <strong>{{ $recipe->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Recipe', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else 
    Invalid recipe selected.
@endif