<div class="character-masterlist-categories">
    @if(!$character->is_myo_slot)
        {!! $character->category->displayName !!} ・ {!! $character->image->species->displayName !!} ・ {!! $character->image->rarity->displayName !!}
    @else
        MYO Slot @if($character->image->species_id) ・ {!! $character->image->species->displayName !!}@endif @if($character->image->rarity_id) ・ {!! $character->image->rarity->displayName !!}@endif
    @endif
</div> 
<h1 class="mb-0">
    @if($character->is_visible && Auth::check() && $character->user_id != Auth::user()->id) 
        <?php $bookmark = Auth::user()->hasBookmarked($character); ?>
        <a href="#" class="btn btn-outline-info float-right bookmark-button" data-id="{{ $bookmark ? $bookmark->id : 0 }}" data-character-id="{{ $character->id }}"><i class="fas fa-bookmark"></i> {{ $bookmark ? 'Edit Bookmark' : 'Bookmark' }}</a> 
    @endif
    @if(!$character->is_visible) <i class="fas fa-eye-slash"></i> @endif {!! $character->displayName !!}
</h1>
<div class="mb-3"> 
    Owned by {!! $character->displayOwner !!}
</div>