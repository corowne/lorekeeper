@extends('galleries.layout')

@section('gallery-title') {{ $gallery->name }} @endsection

@section('gallery-content')
{!! breadcrumbs(['Gallery' => 'gallery', $gallery->name => 'gallery/'.$gallery->id]) !!}

<h1>
    {{ $gallery->name }}
    @if(Auth::check() && $gallery->canSubmit(Auth::user())) <a href="{{ url('gallery/submit/'.$gallery->id) }}" class="btn btn-primary float-right"><i class="fas fa-plus mr-1"></i> Submit</a> @endif
</h1>
<p>{!! nl2br(htmlentities($gallery->description)) !!}</p>

<div>
    {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::text('title', Request::get('title'), ['class' => 'form-control', 'placeholder' => 'Title']) !!}
        </div>
        <div class="form-group mr-3 mb-3">
            {!! Form::select('prompt_id', $prompts, Request::get('prompt_id'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mr-3 mb-3">
            {!! Form::select('sort', [
                'alpha'          => 'Sort Alphabetically (A-Z)',
                'alpha-reverse'  => 'Sort Alphabetically (Z-A)',
                'prompt'         => 'Sort by Prompt (Newest to Oldest)',
                'prompt-reverse' => 'Sort by Prompt (Oldest to Newest)',
                'newest'         => 'Newest First',
                'oldest'         => 'Oldest First'    
            ], Request::get('sort') ? : 'category', ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
</div>

@if($gallery->submissions->count())
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
                    by {!! $submission->credits !!}
                </div>
            </div>
        @endforeach
    </div>
@endforeach

    {!! $submissions->render() !!}
@else
    <p>No submissions found!</p>
@endif

<?php $galleryPage = true; 
$sideGallery = $gallery ?>
@endsection
