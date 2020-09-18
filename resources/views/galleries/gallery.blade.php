@extends('galleries.layout')

@section('gallery-title') {{ $gallery->name }} @endsection

@section('gallery-content')
{!! breadcrumbs(['Gallery' => 'gallery', $gallery->name => 'gallery/'.$gallery->id]) !!}

<h1>
    {{ $gallery->name }}
    @if(Auth::check() && ($gallery->submissions_open || Auth::user()->hasPower('manage_submissions'))) <a href="{{ url('gallery/submit/'.$gallery->id) }}" class="btn btn-primary float-right"><i class="fas fa-plus mr-1"></i> Submit</a> @endif
</h1>
<p>{!! nl2br(htmlentities($gallery->description)) !!}</p>

@if($gallery->submissions->count())
    {!! $submissions->render() !!}

    @foreach($submissions as $submission)
        <p>a</p>
    @endforeach

    {!! $submissions->render() !!}
@else
    <p>No submissions found!</p>
@endif

<?php $galleryPage = true; 
$sideGallery = $gallery ?>
@endsection
