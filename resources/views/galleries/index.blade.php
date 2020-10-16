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
                    @if(Auth::check() && $gallery->canSubmit(Auth::user())) <a href="{{ url('gallery/submit/'.$gallery->id) }}" class="btn btn-primary float-right"><i class="fas fa-plus"></i></a> @endif
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
                    <div class="row">
                        @foreach($gallery->submissions->where('is_visible', 1)->where('status', 'Accepted')->take(4) as $submission)
                            <div class="col-md-3 text-center align-self-center">
                                @include('galleries._thumb', ['submission' => $submission, 'gallery' => true])
                            </div>
                        @endforeach
                    </div>
                    @if($gallery->submissions->where('status', 'Accepted')->count() > 4)
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