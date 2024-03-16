@if ($characters->count())
    <div class="row">
        @foreach ($characters as $character)
            <div class="col-md-3 col-6 text-center mb-2">
                <div>
                    @if(Auth::check() && (Auth::user()->settings->warning_visibility == 0) && isset($character->character_warning) || isset($character->character_warning) && !Auth::check())
                        <a href="{{ $character->url }}"><img src="{{ asset('/images/content_warning.png') }}" class="img-thumbnail" alt="Content Warning for {{ $character->fullName }}"/></a>
                    @else    
                        <a href="{{ $character->url }}"><img src="{{ $character->image->thumbnailUrl }}" class="img-thumbnail" alt="Thumbnail for {{ $character->fullName }}" /></a>
                    @endif
                </div>
                <div class="mt-1">
                    <a href="{{ $character->url }}" class="h5 mb-0">
                        @if (!$character->is_visible)
                            <i class="fas fa-eye-slash"></i>
                        @endif {{ Illuminate\Support\Str::limit($character->fullName, 20, $end = '...') }}
                    </a>
                </div>
                <div class="small">
                    {!! $character->image->species_id ? $character->image->species->displayName : 'No Species' !!} ・ {!! $character->image->rarity_id ? $character->image->rarity->displayName : 'No Rarity' !!}
                    @if(Auth::check() && (Auth::user()->settings->warning_visibility < 2) && isset($character->character_warning) || isset($character->character_warning) && !Auth::check())
                        <p><span class="text-danger"><strong>Character Warning:</strong></span> {!! nl2br(htmlentities($character->character_warning)) !!}</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@else
    <p>No {{ $myo ? 'MYO slots' : 'characters' }} found.</p>
@endif
