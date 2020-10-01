@extends('admin.layout')

@section('admin-title') Gallery Queue @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Gallery Submissions Queue' => 'admin/gallery/pending']) !!}

<h1>
    Gallery Submission Queue
</h1>

<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ set_active('admin/gallery/pending*') }} {{ set_active('admin/gallery') }}" href="{{ url('admin/gallery/pending') }}">Pending</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ set_active('admin/gallery/accepted*') }}" href="{{ url('admin/gallery/accepted') }}">Accepted</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ set_active('admin/gallery/rejected*') }}" href="{{ url('admin/gallery/rejected') }}">Rejected</a>
    </li>
</ul>

<div>
    {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-sm-3 mb-3">
            {!! Form::select('gallery_id', $galleries, Request::get('gallery_id'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
</div>

{!! $submissions->render() !!}

@foreach($submissions as $key=>$submission)
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md mb-4 text-center">
                    <a href="{{ $submission->url }}">{!! $submission->thumbnail !!}</a>
                </div>
                <div class="col-md text-center align-self-center">
                    <h5>{!! $submission->displayName !!}</h5>
                    In {!! $submission->gallery->displayName !!} ・ By {!! $submission->credits !!} 
                    
                    <?php
                        $rejectSum[$key] = 0;
                        $approveSum[$key] = 0;
                        foreach($submission->voteData as $voter=>$vote) {
                            if($vote == 1) $rejectSum[$key] += 1;
                            if($vote == 2) $approveSum[$key] += 1;
                        }
                    ?>
                    <div class="row mt-2">
                        <div class="col-6 text-right text-danger">
                            {{ $rejectSum[$key] }}/{{ $submission->gallery->votes_required }}
                            {!! Form::open(['url' => 'admin/gallery/vote/'.$submission->id.'/reject', 'id' => 'voteRejectForm']) !!}
                                <button class="btn {{ $submission->voteData->get(Auth::user()->id) == 1 ? 'btn-danger' : 'btn-outline-danger' }}" style="min-width:40px;" data-action="reject"><i class="fas fa-times"></i></button>
                            {!! Form::close() !!}
                        </div>
                        <div class="col-6 text-left text-success">
                            {{ $approveSum[$key] }}/{{ $submission->gallery->votes_required }}
                            {!! Form::open(['url' => 'admin/gallery/vote/'.$submission->id.'/accept', 'id' => 'voteApproveForm']) !!}
                                <button class="btn {{ $submission->voteData->get(Auth::user()->id) == 2 ? 'btn-success' : 'btn-outline-success' }}" style="min-width:40px;" data-action="approve"><i class="fas fa-check"></i></button>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

{!! $submissions->render() !!}
<div class="text-center mt-4 small text-muted">{{ $submissions->total() }} result{{ $submissions->total() == 1 ? '' : 's' }} found.</div>

@endsection
