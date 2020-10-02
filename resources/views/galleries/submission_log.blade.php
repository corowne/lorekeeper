@extends('galleries.layout')

@section('gallery-title') {{ $submission->title }} Log @endsection

@section('gallery-content')
{!! breadcrumbs(['gallery' => 'gallery', $submission->gallery->displayName => 'gallery/'.$submission->gallery->id, $submission->title => 'gallery/view/'.$submission->id, 'Log Details' => 'gallery/queue/'.$submission->id ]) !!}

<h1>Log Details</h1>

@include('galleries._queue_submission', ['queue' => false, 'key' => 0])

<div class="row">
    <div class="col-md">
        <div class="card">
            <div class="card-header">
                <h4>Staff Comments</h4>
                {!! Auth::user()->hasPower('staff_comments') ? '(Visible to '.$submission->credits.')' : '' !!}
            </div>
            <div class="card-body">
                (staff comments, etc)
            </div>
        </div>
    </div>
    @if(Auth::user()->hasPower('manage_submissions'))
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Vote Info</h5>
                </div>
                <div class="card-body">
                    @if(isset($submission->vote_data) && $submission->voteData->count())
                        @foreach($submission->voteData as $voter=>$vote)
                            <li>
                                {!! App\Models\User\User::find($voter)->displayName !!} {{ $voter == Auth::user()->id ? '(you)' : '' }}: <span {!! $vote == 2 ? 'class="text-success">Accept' : 'class="text-danger">Reject' !!}</span>
                            </li>
                        @endforeach
                    @else
                        <p>No votes have been cast yet!</p>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

<?php $galleryPage = true; 
$sideGallery = $submission->gallery ?>

@endsection