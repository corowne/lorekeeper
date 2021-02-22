<div class="row world-entry">
    @if($imageUrl)
        <div class="col-md-3 world-entry-image"><a href="{{ $imageUrl }}" data-lightbox="entry" data-title="{{ $name }}"><img src="{{ $imageUrl }}" class="world-entry-image" /></a></div>
    @endif
    <div class="{{ $imageUrl ? 'col-md-9' : 'col-12' }}">
        <h3>
            @if($recipe->needs_unlocking)
                @if(Auth::check() && Auth::user()->hasRecipe($recipe->id))
                    <i class="fas fa-lock-open" data-toggle="tooltip" title="You have this recipe!"></i>
                @else
                    <i class="fas fa-lock" style="opacity:0.5" data-toggle="tooltip" title="You do not have this recipe."></i>
                @endif
            @else
                <i class="fas fa-lock-open" data-toggle="tooltip" title="This recipe is automatically unlocked."></i>
            @endif

            {!! $name !!} @if(isset($idUrl) && $idUrl) <a href="{{ $idUrl }}" class="world-entry-search text-muted"><i class="fas fa-search"></i></a>  @endif
        </h3>


        <div class="row">

            @if($recipe->is_limited)
                <div class="col-md-4">
                    <h5>Requirements</h5>

                    <div class="alert alert-secondary">
                        <?php
                        $limits = [];
                        foreach($recipe->limits as $limit)
                        {
                        $name = $limit->reward->name;
                        $quantity = $limit->quantity > 1 ? $limit->quantity . ' ' : '';
                        $limits[] = $quantity . $name;
                        }
                        echo implode(", ", $limits);
                        ?>
                    </div>
                </div>
            @endif
            <div class="col-md">
                <h5>Ingredients</h5>
                @for($i = 0; $i < count($recipe->ingredients) && $i < 3; ++$i)
                    <?php $ingredient = $recipe->ingredients[$i]?>
                    <div class="alert alert-secondary">
                        @include('home.crafting._recipe_ingredient_entry', ['ingredient' => $ingredient])
                    </div>
                @endfor
                @if(count($recipe->ingredients) > 3)
                    <i class="fas fa-ellipsis-h mb-3"></i>
                @endif
            </div>
            <div class="col-md">
                <h5>Rewards</h5>
                <?php $counter = 0; ?>
                @foreach($recipe->reward_items as $type)
                    @foreach($type as $item)
                        @if($counter > 3) @break @endif
                        <?php ++$counter; ?>
                        <div class="alert alert-secondary">
                            @include('home.crafting._recipe_reward_entry', ['reward' => $item])
                        </div>
                    @endforeach
                @endforeach
                @if($counter > 3)
                    <i class="fas fa-ellipsis-h mb-3"></i>
                @endif
            </div>
        </div>
    </div>
</div>
