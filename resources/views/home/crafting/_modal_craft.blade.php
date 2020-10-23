@if(!$recipe)
    <div class="text-center">Invalid recipe selected.</div>
@else
    @if($recipe->imageUrl)
        <div class="text-center">
            <div class="mb-3"><img class="recipe-image" src="{{ $recipe->imageUrl }}"/></div>
        </div>
    @endif
    <h3>Recipe Details <a class="small inventory-collapse-toggle collapse-toggle" href="#recipeDetails" data-toggle="collapse">Show</a></h3>
    <hr>
    <div class="collapse show" id="recipeDetails">
        <div class="row">
            <div class="col-md-6">
                <h5>Ingredients</h5>
                @foreach($recipe->ingredients as $ingredient)
                    <div class="alert alert-secondary">
                        @include('home.crafting._recipe_ingredient_entry', ['ingredient' => $ingredient])
                    </div>
                @endforeach
            </div>
            <div class="col-md-6">
                <h5>Rewards</h5>
                @foreach($recipe->rewards as $reward)
                    <div class="alert alert-secondary">
                        @include('home.crafting._recipe_reward_entry', ['reward' => $reward])
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    {{-- Check if sufficient ingredients have been selected? --}}
    {!! Form::open(['url' => 'crafting/craft']) !!}
        @include('widgets._inventory_select', ['user' => Auth::user(), 'inventory' => $inventory, 'categories' => $categories, 'selected' => $selected, 'page' => $page])
        <div class="text-right">
            {!! Form::submit('Craft', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
@endif

@include('widgets._inventory_select_js')
<script>
    $(document).keydown(function(e) {
    var code = e.keyCode || e.which;
    if(code == 13)
        return false;
    });
</script>

