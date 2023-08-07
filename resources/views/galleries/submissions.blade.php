@extends('galleries.layout')

@section('gallery-title')
    Gallery Submissions
@endsection

@section('gallery-content')
    {!! breadcrumbs(['gallery' => 'gallery', 'Gallery Submissions' => 'Gallery/Submissions']) !!}

    <h1>
        Gallery Submissions
    </h1>

    <p>This is the log of your gallery submissions. You can submit a piece to a particular gallery by navigating to it and clicking the "submit" button.</p>
    <p>Pending submissions require approval from any collaborators{{ Settings::get('gallery_submissions_require_approval') ? ', as well as staff,' : '' }} before appearing in the gallery.</p>

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link {{ set_active('gallery/submissions/pending') }}" href="{{ url('gallery/submissions/pending') }}">Pending</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ set_active('gallery/submissions/accepted') }}" href="{{ url('gallery/submissions/accepted') }}">Accepted</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ set_active('gallery/submissions/rejected') }}" href="{{ url('gallery/submissions/rejected') }}">Rejected</a>
        </li>
    </ul>

    @if (count($submissions))
        {!! $submissions->render() !!}

        @foreach ($submissions as $key => $submission)
            @include('galleries._queue_submission', ['queue' => true])
        @endforeach

        {!! $submissions->render() !!}
        <div class="text-center mt-4 small text-muted">{{ $submissions->total() }} result{{ $submissions->total() == 1 ? '' : 's' }} found.</div>
    @else
        <p>No gallery submissions found.</p>
    @endif

@endsection
