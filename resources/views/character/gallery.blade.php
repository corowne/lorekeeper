@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title') {{ $character->fullName }}'s Gallery @endsection

@section('meta-img') {{ $character->image->thumbnailUrl }} @endsection

@section('profile-content')
{!! breadcrumbs([($character->is_myo_slot ? 'MYO Slot Masterlist' : 'Character Masterlist') => ($character->is_myo_slot ? 'myos' : 'masterlist'), $character->fullName => $character->url, 'Gallery' => $character->url . '/gallery']) !!}

@include('character._header', ['character' => $character])

<p>These images are user-submitted and should not be confused with the official record of the character's design and history visible <a href="{{ url($character->url . '/images') }}">here</a>.</p>

@if($character->gallerySubmissions->count())
    {!! $submissions->render() !!}

@foreach($submissions->chunk(5) as $chunk)
    <div class="d-flex mb-2">
        @foreach($chunk as $submission)
            <div class="text-center mx-2">
                <a href="{{ $submission->url }}">{!! $submission->thumbnail !!}</a>
                <div class="mt-1">
                    <a href="{{ $submission->url }}" class="h5 mb-0">@if(!$submission->isVisible) <i class="fas fa-eye-slash"></i> @endif {{ $submission->displayTitle }}</a>
                </div>
                <div class="small">
                    In {!! $submission->gallery->displayName !!} ・ By {!! $submission->credits !!}
                </div>
            </div>
        @endforeach
    </div>
@endforeach

    {!! $submissions->render() !!}
@else
    <p>No submissions found!</p>
@endif

@endsection