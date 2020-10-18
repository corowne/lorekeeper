@if(!$recipe)
    <div class="text-center">Invalid recipe selected.</div>
@else
    <div class="text-center">
        <div class="mb-1"><img class="recipe-image" src="{{ $recipe->imageUrl }}"/></div>
    </div>

    <h5>Ingredients</h5>
    @foreach($recipe->ingredients as $ingredient)
        <div class="alert alert-secondary">
            @include('home.crafting._recipe_ingredient_entry', ['ingredient' => $ingredient])
        </div>
    @endforeach

    <h5>Rewards</h5>
    @foreach($recipe->rewards as $reward)
        <div class="alert alert-secondary">
            @include('home.crafting._recipe_reward_entry', ['reward' => $reward])
        </div>
    @endforeach
    {-- Check if sufficient ingredients have been selected? --}
    {!! Form::open(['url' => 'crafting/craft']) !!}
        @include('widgets._inventory_select', ['user' => Auth::user(), 'inventory' => $inventory, 'categories' => $categories, 'selected' => $selected, 'page' => $page])
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

