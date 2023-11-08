@extends('user.layout')

@section('profile-title') {{ $user->name }}'s Characters @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Characters' => $user->url . '/characters']) !!}

<h1>
    {!! $user->displayName !!}'s Characters
</h1>

@if($characters->count())
    <div class="row">
        @foreach($characters as $character)
            <div class="col-md-3 col-6 text-center mb-2">
                <div>
                        @if(Auth::check() && (Auth::user()->settings->warning_visibility == 0) && isset($character->character_warning) || isset($character->character_warning) && !Auth::check())
                        <a href="{{ $character->url }}"><img class="img-thumbnail" src="{{ asset('/images/content_warning.png') }}" alt="Content Warning"/></a>
                        @else    
                        <a href="{{ $character->url }}"><img src="{{ $character->image->thumbnailUrl }}" class="img-thumbnail" alt="Thumbnail for {{ $character->fullName }}"/></a>
                        @endif
                        
                        <div class="mt-1 h5 mb-0">
                        @if(!$character->is_visible) <i class="fas fa-eye-slash"></i> @endif {!! $character->displayName !!}
                        </div>
                        @if(Auth::check() && (Auth::user()->settings->warning_visibility < 2) && isset($character->character_warning) || isset($character->character_warning) && !Auth::check())
                         <div class="small">
                         <p><span class="text-danger"><strong>Character Warning:</strong></span> {!! nl2br(htmlentities($character->character_warning)) !!}</p>
                         </div>
                         @endif
                </div>
            </div>
        @endforeach
    </div>
@else
    <p>No characters found.</p>
@endif

@endsection
