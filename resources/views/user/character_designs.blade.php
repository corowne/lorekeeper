@extends('user.layout')

@section('profile-title')
    @if ($isDesign)
        {{ $user->name }}'s Character Designs
    @else
        {{ $user->name }}'s Character Art
    @endif
@endsection

@section('profile-content')
    @if ($isDesign)
        {!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Character Designs' => $user->url . '/designs']) !!}
    @else
        {!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Character Art' => $user->url . '/designs']) !!}
    @endif

    <h1>
        @if ($isDesign)
            Character Designs
        @else
            Character Art
        @endif
    </h1>

    <div class="row">
        @foreach ($characters as $character)
            <div class="col-md-3 col-6 text-center">
                <div>
                    <a href="{{ $character->url }}"><img src="{{ $character->image->thumbnailUrl }}" class="img-thumbnail" alt="Thumbnail for {{ $character->fullName }}" /></a>
                </div>
                <div class="mt-1">
                    <a href="{{ $character->url }}" class="h5 mb-0">
                        @if (!$character->is_visible)
                            <i class="fas fa-eye-slash"></i>
                        @endif {{ Illuminate\Support\Str::limit($character->fullName, 20, $end = '...') }}
                    </a>
                </div>
                <div class="small">
                    {!! $character->image->species_id ? $character->image->species->displayName : 'No Species' !!} ・ {!! $character->image->rarity_id ? $character->image->rarity->displayName : 'No Rarity' !!} ・ {!! $character->displayOwner !!}
                </div>
            </div>
        @endforeach
    </div>
@endsection
