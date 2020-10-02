@extends('galleries.layout')

@section('gallery-title') Gallery Submissions @endsection

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

@if(count($submissions))
    {!! $submissions->render() !!}

    <div class="row ml-md-2">
        <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-bottom">
            <div class="col-12 col-md-2 font-weight-bold">Gallery</div>
            <div class="col-6 col-md-3 font-weight-bold">Title</div>
            <div class="col-6 col-md-3 font-weight-bold">Collaboration With</div>
            <div class="col-6 col-md-2 font-weight-bold">Submitted</div>
            <div class="col-12 col-md-1 font-weight-bold">Status</div>
        </div>

        @foreach($submissions as $submission)
            <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
            <div class="col-12 col-md-2">{!! $submission->gallery->displayName !!}</div>
            <div class="col-6 col-md-3">{!! $submission->displayName !!}</div>
            <div class="col-6 col-md-3">
                @if($submission->collaborators->count())
                    @foreach($submission->collaborators as $collaborator)
                        {!! $collaborator->user_id != Auth::user()->id ? $collaborator->user->displayName : '' !!}
                    @endforeach
                @else
                -
                @endif
            </div>
            <div class="col-6 col-md-2">{!! pretty_date($submission->created_at) !!}</div>
            <div class="col-6 col-md-1 text-right">
                <span class="btn btn-{{ $submission->status == 'Pending' ? 'secondary' : ($submission->status == 'Accepted' ? 'success' : 'danger') }} btn-sm py-0 px-1">{{ $submission->status }}</span>
            </div>
            <div class="col-6 col-md-1"><a href="{{ $submission->queueUrl }}" class="btn btn-primary btn-sm py-0 px-1">Details</a></div>
            </div>
        @endforeach
    </div>

    {!! $submissions->render() !!}
    <div class="text-center mt-4 small text-muted">{{ $submissions->total() }} result{{ $submissions->total() == 1 ? '' : 's' }} found.</div>
@else
    <p>No gallery submissions found.</p>
@endif

<?php $galleryPage = false; 
$sideGallery = null ?>

@endsection
