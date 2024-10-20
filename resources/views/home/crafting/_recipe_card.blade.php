<div class="card mb-3" data-id="{{ $recipe->id }}" data-name="{{ $recipe->name }}">
    <div class="card-header">
        <h2 class="mb-0">{{ $recipe->name }}</h2>
    </div>
    <div class="card-body text-center">
        @if(isset($recipe->image_url))
            <div class="text-center mb-3">
                <img src="{{ $recipe->imageUrl }}" class="recipe-image">
            </div>
        @endif
        <div class="row">
            <div class="col-md-6">
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
            <div class="col-md-6">
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
            @if($recipe->is_limited)
            <div class="text-danger small">(Requires <?php 
                $limits = []; 
                foreach($recipe->limits as $limit)
                {
                $name = $limit->reward->name;
                $limits[] = $name;
                }
                echo implode(", ", $limits);
            ?>)</div>
            @endif
        </div>
        <a class="btn btn-primary btn-block btn-craft" href="">Craft</a>
    </div>
</div>