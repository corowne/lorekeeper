@extends('user.layout')

@section('profile-title')
    {{ $user->name }}'s Favorites
@endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Character Designs' => $user->url . '/designs']) !!}

    <h1>
        Designs
    </h1>

    <div class="row">
        @foreach ($designs as $character)
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
