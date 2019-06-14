@if($character->parsed_description)
    <div class="parsed-text">{!! $character->parsed_description !!}</div>
@else 
    No additional notes given.
@endif
@if(Auth::check() && Auth::user()->hasPower('manage_masterlist'))
    <div class="mt-3">
        <a href="#" class="btn btn-outline-info btn-sm edit-description" data-slug="{{ $character->slug }}"><i class="fas fa-cog"></i> Edit</a>
    </div>
@endif