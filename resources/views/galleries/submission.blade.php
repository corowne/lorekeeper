@extends('galleries.layout')

@section('gallery-title') {{ $submission->title }} @endsection

@section('gallery-content')
{!! breadcrumbs(['gallery' => 'gallery', $submission->gallery->displayName => 'gallery/'.$submission->gallery->id, $submission->title => 'gallery/view/'.$submission->id ]) !!}

<h1>
    @if(!$submission->isVisible) <i class="fas fa-eye-slash"></i> @endif {{ $submission->title }}
    <div class="float-right">
        @if(Auth::check())
            {!! Form::open(['url' => '/gallery/favorite/'.$submission->id]) !!} 
                @if($submission->user->id != Auth::user()->id && $submission->collaborators->where('user_id', Auth::user()->id)->first() == null && $submission->isVisible)
                    {!! Form::button('<i class="fas fa-star"></i> ', ['class' => 'btn btn'. ($submission->favorites->where('user_id', Auth::user()->id)->first() == null ? 'btn-outline-primary' : 'btn-primary'), 'data-toggle' => 'tooltip', 'title' => ($submission->favorites->where('user_id', Auth::user()->id)->first() == null ? 'Add to' : 'Remove from').' your Favorites', 'type' => 'submit']) !!}
                @endif     
                @if($submission->user->id == Auth::user()->id || Auth::user()->hasPower('manage_submissions'))
                    <a class="btn btn-outline-primary" href="/gallery/edit/{{ $submission->id }}"><i class="fas fa-edit"></i> Edit</a>
                @endif
            {!! Form::close() !!}
        @endif
    </div>
</h1>
<div class="mb-3 mb-sm-4"> 
    <div class="row">
        <div class="col-md">
            In {!! $submission->gallery->displayName !!} ・ 
            By {!! $submission->credits !!}
        </div>
        <div class="col-md text-right">
            {{ $submission->favorites->count() }} Favorite{{ $submission->favorites->count() != 1 ? 's' : ''}} ・ {{ Laravelista\Comments\Comment::where('commentable_type', 'App\Models\Gallery\GallerySubmission')->where('commentable_id', $submission->id)->count() }} Comment{{ Laravelista\Comments\Comment::where('commentable_type', 'App\Models\Gallery\GallerySubmission')->where('commentable_id', $submission->id)->count() != 1 ? 's' : ''}}
        </div>
    </diV>
</div>

<!-- Main Content -->
@if(isset($submission->parsed_text) && $submission->parsed_text) <div class="card mx-md-4 mb-4"><div class="card-body"> @endif
    @if(isset($submission->hash) && $submission->hash)
        <div class="text-center mb-4">
            <a href="{{ $submission->imageUrl }}" data-lightbox="entry" data-title="{{ $submission->title }}">
                <img src="{{ $submission->imageUrl }}" class="image" style="max-width:100%; {{ isset($submission->parsed_text) && $submission->parsed_text ? 'max-height:50vh;' : 'max-height:70vh;' }} border-radius:.5em;" data-toggle="tooltip" title="Click to view larger size"/>
            </a>
        </div>
    @endif
    @if(isset($submission->parsed_text) && $submission->parsed_text)
        {!! $submission->parsed_text !!}
    @endif
@if(isset($submission->parsed_text) && $submission->parsed_text) </div></div> @endif

<!-- Submission Info -->
<div class="row mx-md-2 mb-4">
    <div class="col-md mb-4">
        <div class="row mb-4 no-gutters">
            <div class="col-md-2 mb-4 mobile-hide text-center">
                <a href="/user/{{ $submission->user->name }}"><img src="/images/avatars/{{ $submission->user->avatar }}" style="border-radius:50%; margin-right:25px; max-width:100%;" data-toggle="tooltip" title="{{ $submission->user->name }}"/></a>
            </div>
            <div class="col-md ml-md-2">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ $submission->title }}</h5>
                        <div class="float-right">
                            @if(Auth::check() && ($submission->user->id != Auth::user()->id && $submission->collaborators->where('user_id', Auth::user()->id)->first() == null) && $submission->isVisible)
                                {!! Form::open(['url' => '/gallery/favorite/'.$submission->id]) !!} 
                                    {{ $submission->favorites->count() }} {!! Form::button('<i class="fas fa-star"></i> ', ['style' => 'border:0; border-radius:.5em;', 'class' => ($submission->favorites->where('user_id', Auth::user()->id)->first() != null ? 'btn-success' : ''), 'data-toggle' => 'tooltip', 'title' => ($submission->favorites->where('user_id', Auth::user()->id)->first() == null ? 'Add to' : 'Remove from').' your Favorites', 'type' => 'submit']) !!} ・ {{ Laravelista\Comments\Comment::where('commentable_type', 'App\Models\Gallery\GallerySubmission')->where('commentable_id', $submission->id)->count() }} <i class="fas fa-comment"></i>
                                {!! Form::close() !!}
                            @else
                                {{ $submission->favorites->count() }} <i class="fas fa-star" data-toggle="tooltip" title="Favorites"></i> ・ {{ Laravelista\Comments\Comment::where('commentable_type', 'App\Models\Gallery\GallerySubmission')->where('commentable_id', $submission->id)->count() }} <i class="fas fa-comment" data-toggle="tooltip" title="Comments"></i>
                            @endif
                        </div>
                        In {!! $submission->gallery->displayName !!} ・ By {!! $submission->credits !!}
                    </div>
                    <div class="card-body">
                        {!! $submission->parsed_description ? $submission->parsed_description : '<i>No description provided.</i>' !!}

                        <hr/>
                        <p>
                            <strong>Submitted By</strong> {!! $submission->user->displayName !!}
                            @if($submission->prompt_id)
                                <strong>for</strong> {!! $submission->prompt->displayName !!}
                            @endif
                            <br/>
                            <strong>Submitted:</strong> {!! pretty_date($submission->created_at) !!} ・ 
                            <strong>Last Edited:</strong> {!! pretty_date($submission->updated_at) !!}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($submission->collaborators->count() || $submission->characters->count())
        <div class="col-md-4 col-lg-3">
            @if($submission->collaborators->count())
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Collaboration Info</h5>
                    </div>
                    <div class="card-body">
                        @if($submission->status == 'Pending' && Auth::check() && $submission->collaborators->where('user_id', Auth::user()->id)->first() != null)
                            <p>Check that your role in the collaboration is correct as listed, and if not, make any changes. You can also remove yourself from the collaborator list if necessary. When you are done, press "submit" to make any changes as well as approve the submission/the record of your contribution to it. You will be able to edit this until the submission is approved.</p>
                            {!! Form::open(['url' => '/gallery/collaborator/'.$submission->id]) !!}
                                @foreach($submission->collaborators as $collaborator)
                                    @if($collaborator->user_id == Auth::user()->id)
                                    <div class="mb-2">
                                        <div class="d-flex">{!! $collaborator->has_approved ? '<div class="mb-2 mr-2 text-success" data-toggle="tooltip" title="Has Approved"><i class="fas fa-check"></i></div>' : '' !!}{!! $collaborator->user->displayName !!}:
                                            <div class="float-right">
                                                {!! Form::label('remove_user', 'Remove Me', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If toggled on, this will remove the record of your collaboration from this submission.') !!}
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            {!! Form::text('collaborator_data[]', $collaborator->data, ['class' => 'form-control mr-2', 'placeholder' => 'Role (Sketch, Lines, etc.)']) !!}
                                            {!! Form::checkbox('remove_user', 1, false, ['class' => 'form-check-input', 'data-toggle' => 'toggle', 'data-onstyle' => 'danger']) !!}
                                        </div>
                                    </div>
                                    @else
                                        <div class="d-flex">
                                            {!! $collaborator->has_approved ? '<div class="mb-2 mr-2 text-success" data-toggle="tooltip" title="Has Approved"><i class="fas fa-check"></i></div>' : '' !!} {!! $collaborator->user->displayName !!}: {{ $collaborator->data }}
                                        </div>
                                    @endif
                                @endforeach
                                <div class="mt-2 text-right">
                                    {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
                                </div>
                            {!! Form::close() !!}
                        @else
                            @foreach($submission->collaborators as $collaborator)
                                <div class="d-flex">
                                    {!! $submission->status == 'Pending' && $collaborator->has_approved ? '<div class="mb-2 mr-2 text-success" data-toggle="tooltip" title="Has Approved"><i class="fas fa-check"></i></div>' : '' !!} {!! $collaborator->user->displayName !!}: {{ $collaborator->data }}
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endif
            @if($submission->characters->count())
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Characters</h5>
                    </div>
                    <div class="card-body">
                        @foreach($submission->characters->chunk(1) as $chunk)
                            <div class="row mb-2">
                                @foreach($chunk as $character)
                                    <div class="col-md">
                                        @include('galleries._character', ['character' => $character->character])
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>

<!-- Comments -->
@if($submission->isVisible)
    <div class="container">
        @comments(['model' => $submission,
                'perPage' => 5
            ])
    </div>
@endif

<?php $galleryPage = true; 
$sideGallery = $submission->gallery ?>

@endsection
