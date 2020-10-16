@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title') {{ $character->fullName }}'s Gallery @endsection

@section('meta-img') {{ $character->image->thumbnailUrl }} @endsection

@section('profile-content')
{!! breadcrumbs([($character->is_myo_slot ? 'MYO Slot Masterlist' : 'Character Masterlist') => ($character->is_myo_slot ? 'myos' : 'masterlist'), $character->fullName => $character->url, 'Gallery' => $character->url . '/gallery']) !!}

@include('character._header', ['character' => $character])

<p>These images are user-submitted and should not be confused with the official record of the character's design and history visible <a href="{{ url($character->url . '/images') }}">here</a>.</p>

@if($character->gallerySubmissions->count())
    {!! $submissions->render() !!}

<div class="d-flex align-content-around flex-wrap mb-2">
    @foreach($submissions as $submission)
        @include('galleries._thumb', ['submission' => $submission, 'gallery' => false])
    @endforeach
</div>

    {!! $submissions->render() !!}
@else
    <p>No submissions found!</p>
@endif

@endsection