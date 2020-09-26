@extends('galleries.layout')

@section('gallery-title') Home @endsection

@section('gallery-content')
{!! breadcrumbs(['Gallery' => 'gallery']) !!}
<h1>Gallery</h1>

@if($galleries->count())
    {!! $galleries->render() !!}

    @foreach($galleries as $gallery)
        <div class="card mb-4">
            <div class="card-header">
                <h4>
                    {!! $gallery->displayName !!}
                    @if(Auth::check() && ($gallery->submissions_open || Auth::user()->hasPower('manage_submissions'))) <a href="{{ url('gallery/submit/'.$gallery->id) }}" class="btn btn-primary float-right"><i class="fas fa-plus"></i></a> @endif
                </h4>
                @if($gallery->children->count())
                    <p>
                        Sub-galleries: 
                        @foreach($gallery->children as $count=>$child)
                            {!! $child->displayName !!}{{ $count < $gallery->children->count() - 1 ? ', ' : '' }}
                        @endforeach
                    </p>
                @endif
            </div>
            <div class="card-body">
                @if($gallery->submissions->where('status', 'Accepted')->count())
                    <div class="d-flex">
                        @foreach($gallery->submissions->where('status', 'Accepted')->take(5) as $submission)
                            <div class="text-center mx-2">
                                <div>
                                    <a href="{{ $submission->url }}"><img src="{{ $submission->thumbnailUrl }}" class="img-thumbnail" /></a>
                                </div>
                                <div class="mt-1">
                                    <a href="{{ $submission->url }}" class="h5 mb-0">@if(!$submission->isVisible) <i class="fas fa-eye-slash"></i> @endif {{ $submission->title }}</a>
                                </div>
                                <div class="small">
                                    by {!! $submission->credits !!}
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($gallery->submissions->count() > 5)
                        <div class="text-right"><a href="{{ url('gallery/'.$gallery->id) }}">See More...</a></div>
                    @endif
                @else
                    <p>This gallery has no submissions!</p>
                @endif
            </div>
        </div>
    @endforeach

    {!! $galleries->render() !!}
@else
    <p>There aren't any galleries!</p>
@endif

<?php $galleryPage = false; 
$sideGallery = null ?>

@endsection