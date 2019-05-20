@extends('character.layout')

@section('profile-title') {{ $character->fullName }} @endsection

@section('profile-content')
{!! breadcrumbs(['Masterlist' => 'masterlist', $character->fullName => $character->url]) !!}

@include('character._header', ['character' => $character])

<div class="row mb-3">
    {{-- Main Image --}}
    <div class="text-center col-md-7">
        <a href="{{ $character->image->imageUrl }}" data-lightbox="entry" data-title="{{ $character->fullName }}">
            <img src="{{ $character->image->imageUrl }}" class="image" />
        </a>
    </div>
    @include('character._image_info', ['image' => $character->image])
    
</div>

{{-- Info --}}
<div class="card character-bio">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link active" id="statsTab" data-toggle="tab" href="#stats" role="tab">Stats</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="notesTab" data-toggle="tab" href="#notes" role="tab">Description</a>
            </li>
        </ul>
    </div>
    <div class="card-body tab-content">
        <div class="tab-pane fade show active" id="stats">
            @include('character._tab_stats', ['character' => $character])
        </div>
        <div class="tab-pane fade" id="notes">
            @include('character._tab_notes', ['character' => $character])
        </div>
    </div>
</div>

@endsection
