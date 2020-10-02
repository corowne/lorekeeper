@extends('galleries.layout')

@section('gallery-title') {{ $submission->title }} Log @endsection

@section('gallery-content')
{!! breadcrumbs(['gallery' => 'gallery', $submission->gallery->displayName => 'gallery/'.$submission->gallery->id, $submission->title => 'gallery/view/'.$submission->id, 'Log' => 'gallery/queue/'.$submission->id ]) !!}

<h1>Log Details</h1>

@include('galleries._queue_submission', ['queue' => false, 'key' => 0])

<?php $galleryPage = true; 
$sideGallery = $submission->gallery ?>

@endsection