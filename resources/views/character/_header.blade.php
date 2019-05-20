<div class="character-masterlist-categories">
    {!! $character->category->displayName !!} ・ {!! $character->image->species->displayName !!} ・ {!! $character->image->rarity->displayName !!}
</div>
<h1 class="mb-0">
    {!! $character->displayName !!}
</h1>
<div class="mb-3"> 
    Owned by {!! $character->displayOwner !!}
</div>