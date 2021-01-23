@extends('world.layout')

@section('title') {{ $recipe->name }} @endsection

@section('meta-img') {{ $recipe->imageUrl ? $recipe->imageUrl : null }} @endsection

@section('meta-desc')
    {!! substr(str_replace('"','&#39;',$recipe->description),0,69) !!}
@endsection

@section('content')
{!! breadcrumbs(['World' => 'world', 'recipes' => 'world/recipes', $recipe->name => $recipe->idUrl]) !!}

<div class="row">
    <div class="col-lg-6 col-lg-10 mx-auto">
        <div class="card mb-3">
            <div class="card-body">
                @if($recipe->imageUrl)
                    <div class="world-entry-image text-center mb-2"><a href="{{ $recipe->imageUrl }}" data-lightbox="entry" data-title="{{ $recipe->name }}"><img src="{{ $recipe->imageUrl }}" class="world-entry-image mw-100" style="max-height:300px;" /></a></div>
                @endif

                <div>
                    <h1>
                        @if($recipe->needs_unlocking)
                            @if(Auth::check() && Auth::user()->hasRecipe($recipe->id))
                                <i class="fas fa-lock-open" data-toggle="tooltip" title="You have this recipe!"></i>
                            @else
                                <i class="fas fa-lock" style="opacity:0.5" data-toggle="tooltip" title="You do not have this recipe."></i>
                            @endif
                        @else
                            <i class="fas fa-lock-open" data-toggle="tooltip" title="This recipe is automatically unlocked."></i>
                        @endif
                        {!! $recipe->name !!}
                    </h1>
                    <div class="world-entry-text">
                        {!! $recipe->description !!}
                    </div>

                    <div class="row">

                        @if($recipe->is_limited)
                            <div class="col-md-12">
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
                            @foreach($recipe->reward_items as $type)
                                @foreach($type as $item)
                                    <div class="alert alert-secondary">
                                        @include('home.crafting._recipe_reward_entry', ['reward' => $item])
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>

                    @if(!$recipe->needs_unlocking || (Auth::check() && Auth::user()->hasRecipe($recipe->id)))
                        <div class="text-center">
                            <h5><a href="{{ url('crafting') }}" class="btn btn-primary">
                                Craft this from your Recipe Book!
                            </a></h5>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
