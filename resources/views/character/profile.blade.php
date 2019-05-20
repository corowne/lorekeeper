@extends('character.layout')

@section('profile-title') {{ $character->fullName }} @endsection

@section('profile-content')
{!! breadcrumbs(['Masterlist' => 'masterlist', $character->fullName => $character->url]) !!}

@include('character._header', ['character' => $character])

<div class="text-center mb-3">
    <a href="{{ $character->image->imageUrl }}" data-lightbox="entry" data-title="{{ $character->slug }}">
        <img src="{{ $character->image->imageUrl }}" class="image" />
    </a>
</div>

{{-- Bio --}}
@if($character->profile->parsed_description)
    <div class="card mb-3">
        @if($character->profile->parsed_description)
            <div class="card-body parsed-text">
                {!! $character->profile->parsed_description !!}

            </div>
        @endif
    </div>
@endif
    <div class="card mb-3">
        <div class="card-body parsed-text">
            Custom character profile content goes here.

        </div>

    </div>

@if($character->is_trading || $character->is_gift_art_allowed)
    <div class="card mb-3">
        @if($character->is_trading || $character->is_gift_art_allowed)
            <ul class="list-group list-group-flush">
                @if($character->is_gift_art_allowed)
                    <li class="list-group-item"><h5 class="mb-0"><i class="text-success far fa-circle fa-fw mr-2"></i> Gift art is allowed</h5></li>
                @endif
                @if($character->is_trading)
                    <li class="list-group-item"><h5 class="mb-0"><i class="text-success far fa-circle fa-fw mr-2"></i> Open for trades</h5></li>
                @endif
            </ul>
        @endif
    </div>
@endif
@endsection