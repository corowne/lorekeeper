@extends('galleries.layout')

@section('gallery-title') {{ $gallery->name }} @endsection

@section('gallery-content')
{!! breadcrumbs(['Gallery' => 'gallery', $gallery->name => 'gallery/'.$gallery->id]) !!}

<h1>
    {{ $gallery->name }}
    @if(Auth::check() && $gallery->canSubmit(Auth::user())) <a href="{{ url('gallery/submit/'.$gallery->id) }}" class="btn btn-primary float-right"><i class="fas fa-plus mr-1"></i> Submit</a> @endif
</h1>
<p>{!! $gallery->description !!}</p>

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
                'newest'         => 'Newest First',
                'oldest'         => 'Oldest First',
                'alpha'          => 'Sort Alphabetically (A-Z)',
                'alpha-reverse'  => 'Sort Alphabetically (Z-A)',
                'prompt'         => 'Sort by Prompt (Newest to Oldest)',
                'prompt-reverse' => 'Sort by Prompt (Oldest to Newest)',    
            ], Request::get('sort') ? : 'category', ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
</div>

@if($gallery->submissions->count())
    {!! $submissions->render() !!}

<div class="d-flex align-content-around flex-wrap mb-2">
    @foreach($submissions as $submission)
        @include('galleries._thumb', ['submission' => $submission, 'gallery' => true])
    @endforeach
</div>

    {!! $submissions->render() !!}
@else
    <p>No submissions found!</p>
@endif

<?php $galleryPage = true; 
$sideGallery = $gallery ?>
@endsection
