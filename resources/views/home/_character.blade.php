@if($character)
    <div class="character-info" data-id="{{ $character->id }}"><img src="{{ $character->image->thumbnailUrl }}" class="mw-100" /></div>
    <div class="text-center">{{ $character->slug }}</div>
@else 
    <div class="text-danger character-info" data-id="0">Character not found.</div>
@endif