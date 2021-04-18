<li class="list-group-item">
    <a class="card-title h5 collapse-title"  data-toggle="collapse" href="#redeemRecipe"> Redeem Recipe</a>
    <div id="redeemRecipe" class="collapse">
        {!! Form::hidden('tag', $tag->tag) !!}
        <p>This action is not reversible. Are you sure you want to redeem a recipe?</p>
        <div class="text-right">
            {!! Form::button('Redeem', ['class' => 'btn btn-primary', 'name' => 'action', 'value' => 'act', 'type' => 'submit']) !!}
        </div>
    </div>
</li>
