@extends('admin.layout')

@section('admin-title') Forums @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Forums' => 'admin/forums']) !!}

<h1 class="float-md-left">Forums</h1>
<div class="float-md-right mb-3"><a class="btn btn-primary" href="{{ url('admin/forums/create') }}"><i class="fas fa-plus"></i> Create New Forum</a></div>

<p style="clear:both">Here you can create forums for users to create threads in.</p>

@if(!count($forums))
    <p>No forums found.</p>
@else
    {!! $forums->render() !!}
        <div class="row ml-md-2">
            <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
                <div class="col-12 col-md-4 font-weight-bold text-center text-md-left">Name</div>
                <div class="col-12 col-md-5 font-weight-bold text-center text-md-left">Children</div>
                <div class="col-12 col-md-3 font-weight-bold">Last Edited</div>
            </div>
            @foreach($forums as $forum)
                <div class="d-flex row flex-wrap col-12 mt-1 pt-2 px-0 ubt-top">
                    <div class="col-12 col-md-4 text-center text-md-left">{!! $forum->displayName !!}</div>
                    <div class="col-12 col-md-5 text-center text-md-left">
                        @if($forum->children->count())
                            <small>@foreach($forum->children as $child) {!! $child->displayName !!} {!! $loop->last ? '' : ',' !!} @endforeach</small>
                        @else
                            <small>-</small>
                        @endif
                    </div>
                    <div class="col-6 col-md-2">{!! pretty_date($forum->updated_at) !!}</div>
                    <div class="col-6 col-md-1 text-right"><a href="{{ url('admin/forums/edit/'.$forum->id) }}" class="btn btn-primary py-0 px-2">Edit</a></div>
                </div>
            @endforeach
        </div>
    {!! $forums->render() !!}

    <div class="text-center mt-4 small text-muted">{{ $forums->total() }} result{{ $forums->total() == 1 ? '' : 's' }} found.</div>

@endif

@endsection
