@extends('user.layout')

@section('profile-title') {{ $user->name }}'s Gallery @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Gallery' => $user->url . '/gallery']) !!}

<h1>
    Gallery
</h1>

@if($user->gallerySubmissions->count())
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
