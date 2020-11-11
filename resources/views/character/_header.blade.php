<div class="character-masterlist-categories">
    @if(!$character->is_myo_slot)
        {!! $character->category->displayName !!} ・ {!! $character->image->species->displayName !!} ・ {!! $character->image->rarity->displayName !!}
    @else
        MYO Slot @if($character->image->species_id) ・ {!! $character->image->species->displayName !!}@endif @if($character->image->rarity_id) ・ {!! $character->image->rarity->displayName !!}@endif
    @endif
</div> 
<h1 class="mb-0">
    @if($character->is_visible && Auth::check() && $character->user_id != Auth::user()->id) 
        @if(Config::get('lorekeeper.extensions.character_status_badges'))
            <!-- character trade/gift status badges -->
            <div class="col-md-4 text-center text-md-right my-auto">
                <h1>
                <span class="badge {{ $character->is_trading ? 'badge-success' : 'badge-danger' }}" data-toggle="tooltip" title="{{ $character->is_trading ? 'OPEN for sale and trade offers.' : 'CLOSED for sale and trade offers.' }}"><i class="fas fa-comments-dollar"></i></span>
                @if(!$character->is_myo_slot)
                    <span class="badge {{ $character->is_gift_art_allowed == 1 ? 'badge-success' : ($character->is_gift_art_allowed == 2 ? 'badge-warning' : 'badge-danger') }}" data-toggle="tooltip" title="{{ $character->is_gift_art_allowed == 1 ? 'OPEN for gift art.' : ($character->is_gift_art_allowed == 2 ? 'PLEASE ASK before gift art.' : 'CLOSED for gift art.') }}"><i class="fas fa-pencil-ruler"></i></span>
                    <span class="badge {{ $character->is_gift_writing_allowed == 1 ? 'badge-success' : ($character->is_gift_writing_allowed == 2 ? 'badge-warning' : 'badge-danger') }}" data-toggle="tooltip" title="{{ $character->is_gift_writing_allowed == 1 ? 'OPEN for gift art.' : ($character->is_gift_writing_allowed == 2 ? 'PLEASE ASK before gift art.' : 'CLOSED for gift art.') }}"><i class="fas fa-pencil-ruler"></i></span>
                @endif
                </h1>
            </div>
        @endif
        <?php $bookmark = Auth::user()->hasBookmarked($character); ?>
        <a href="#" class="btn btn-outline-info float-right bookmark-button" data-id="{{ $bookmark ? $bookmark->id : 0 }}" data-character-id="{{ $character->id }}"><i class="fas fa-bookmark"></i> {{ $bookmark ? 'Edit Bookmark' : 'Bookmark' }}</a> 
    @endif
    @if(Config::get('lorekeeper.extensions.character_TH_profile_link') && $character->profile->link)
        <a class="btn btn-outline-info" data-character-id="{{ $character->id }}" href="{!! $character->profile->link !!}"><i class="fas fa-home"></i> Profile</a>
    @endif
    @if(!$character->is_visible) <i class="fas fa-eye-slash"></i> @endif {!! $character->displayName !!}
</h1>
<div class="mb-3"> 
    Owned by {!! $character->displayOwner !!}
</div>