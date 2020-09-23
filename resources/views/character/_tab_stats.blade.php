<div class="row">
    <div class="col-lg-3 col-4"><h5>Owner</h5></div>
    <div class="col-lg-9 col-8">{!! $character->displayOwner !!}</div>
</div>
@if(!$character->is_myo_slot)
    <div class="row">
        <div class="col-lg-3 col-4"><h5>Category</h5></div>
        <div class="col-lg-9 col-8">{!! $character->category->displayName !!}</div>
    </div>
@endif
<div class="row">
    <div class="col-lg-3 col-4"><h5 class="mb-0">Created</h5></div>
    <div class="col-lg-9 col-8">{!! format_date($character->created_at) !!}</div>
</div>
@if(!$parent)
    <hr />

    <h5><i class="text-{{ $character->is_giftable ? 'success far fa-circle' : 'danger fas fa-times'  }} fa-fw mr-2"></i> {{ $character->is_giftable ? 'Can' : 'Cannot'  }} be gifted</h5>
    <h5><i class="text-{{ $character->is_tradeable ? 'success far fa-circle' : 'danger fas fa-times'  }} fa-fw mr-2"></i> {{ $character->is_tradeable ? 'Can' : 'Cannot'  }} be traded</h5>
    <h5><i class="text-{{ $character->is_sellable ? 'success far fa-circle' : 'danger fas fa-times'  }} fa-fw mr-2"></i> {{ $character->is_sellable ? 'Can' : 'Cannot'  }} be sold</h5>
    <div class="row">
        <div class="col-lg-3 col-4"><h5>Sale Value</h5></div>
        <div class="col-lg-9 col-8">${{ $character->sale_value }}</div>
    </div>
@endif
@if($character->transferrable_at && $character->transferrable_at->isFuture())
    <div class="row">
        <div class="col-lg-3 col-4"><h5>Cooldown</h5></div>
        <div class="col-lg-9 col-8">Cannot be transferred until {!! format_date($character->transferrable_at) !!}</div>
    </div>
@endif

@if($parent)
    <hr />
    
    <h5 class="text-center">Bound To {!! add_help('This character or add-on is bound to another character which controls the ownership.') !!}</h5>
    <div class="row justify-content-center text-center">
        <div class="col-md-3">
            <a href="{{ $parent->parent->url }}"><img src="{{ $parent->parent->image->thumbnailUrl }}" class="img-thumbnail" /></a><br />
            <a href="{{ $parent->parent->url }}" class="h5 mb-0">@if(!$parent->parent->is_visible) <i class="fas fa-eye-slash"></i> @endif {{ $parent->parent->fullName }}</a>
        <div class="small">
            {!! $parent->parent->image->species_id ? $parent->parent->image->species->displayName : 'No Species' !!} ・ {!! $parent->parent->image->rarity_id ? $parent->parent->image->rarity->displayName : 'No Rarity' !!} ・ {!! $parent->parent->displayOwner !!}
        </div>
        </div>
    </div>
@endif

@if($children->count())
    <hr />

    <h5 class="text-center">Binding {!! add_help('This character is in possession of the following add-ons or characters and controls their ownership.') !!}</h5>
    <div class="row justify-content-center text-center">
    @foreach($children as $link)
        <div class="col-md-3">
            <a href="{{ $link->child->url }}"><img src="{{ $link->child->image->thumbnailUrl }}" class="img-thumbnail" /></a><br />
            <a href="{{ $link->child->url }}" class="h5 mb-0">@if(!$link->child->is_visible) <i class="fas fa-eye-slash"></i> @endif {{ $link->child->fullName }}</a>
        <div class="small">
            {!! $link->child->image->species_id ? $link->child->image->species->displayName : 'No Species' !!} ・ {!! $link->child->image->rarity_id ? $link->child->image->rarity->displayName : 'No Rarity' !!} ・ {!! $link->child->displayOwner !!}
        @if($link->child->character_category_id == 6)
            <?php $features = $link->child->image->features()->with('feature.category')->get(); ?>
            @if($features->count())
                @foreach($features as $feature)
                    <div>@if($feature->feature->feature_category_id) <strong>{!! $feature->feature->category->displayName !!}:</strong> @endif {!! $feature->feature->displayName !!} @if($feature->data) ({{ $feature->data }}) @endif</div> 
                @endforeach
            @endif
        @endif
        
        </div>
        </div>
    @endforeach
    </div>
@endif

@if(Auth::check() && Auth::user()->hasPower('manage_characters'))
    <div class="mt-3">
        <a href="#" class="btn btn-outline-info btn-sm edit-stats" data-{{ $character->is_myo_slot ? 'id' : 'slug' }}="{{ $character->is_myo_slot ? $character->id : $character->slug }}"><i class="fas fa-cog"></i> Edit</a>
    </div>
@endif