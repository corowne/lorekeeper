@extends('user.layout')

@section('profile-title') {{ $user->name }}'s Favorites @endsection

@section('profile-content')
@if($characters)
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Favorites' => $user->url . '/favorites', 'Own Characters' => $user->url . '/favorites/own-characters']) !!}
@else
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Favorites' => $user->url . '/favorites']) !!}
@endif

<h1>
    {{ $characters ? 'Own Character ' : '' }}Favorites
</h1>

@if($characters)
    <p>These are {{ Auth::check() && Auth::user()->id == $user->id ? 'your' : $user->name.'\'s' }} favorites which feature <a href="{{ url($user->url . '/characters') }}">characters {{ Auth::check() && Auth::user()->id == $user->id ? 'you' : 'they' }} own</a>.</p>
@endif

@if($user->galleryFavorites->count())

    {!! $favorites->render() !!}

@foreach($favorites->chunk(5) as $chunk)
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

    {!! $favorites->render() !!}
@else
    <p>No favorites found!</p>
@endif

@endsection
