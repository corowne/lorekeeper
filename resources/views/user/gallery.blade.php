@extends('user.layout')

@section('profile-title')
    {{ $user->name }}'s Gallery
@endsection

@section('profile-content')
    {!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Gallery' => $user->url . '/gallery']) !!}

    <h1>
        Gallery
    </h1>

    @if ($user->gallerySubmissions->count())
        {!! $submissions->render() !!}

        <div class="d-flex align-content-around flex-wrap mb-2">
            @foreach ($submissions as $submission)
                @include('galleries._thumb', ['submission' => $submission, 'gallery' => false])
            @endforeach
        </div>

        {!! $submissions->render() !!}
    @else
        <p>No submissions found!</p>
    @endif

@endsection
