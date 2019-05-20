@if($character->parsed_description)
    <div class="parsed-text">{!! $character->parsed_description !!}</div>
@else 
    No additional notes given.
@endif