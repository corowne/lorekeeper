@extends('admin.layout')

@section('admin-title') Galleries @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Galleries' => 'admin/data/galleries']) !!}

<h1>Galleries</h1>

<p>This is a list of galleries that art and literature can be submitted to.</p>

<div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/data/galleries/create') }}"><i class="fas fa-plus"></i> Create New Gallery</a></div>
@if(!count($galleries))
    <p>No galleries found.</p>
@else
    {!! $galleries->render() !!}

    <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-bottom">
        <div class="col-6 col-md-1 font-weight-bold">Open</div>
        <div class="col-6 col-md-2 font-weight-bold">Name</div>
        <div class="col-6 col-md-1 font-weight-bold">{{ Settings::get('gallery_submissions_reward_currency') ? 'Rewards' : '' }}</div>
        <div class="col-6 col-md-2 font-weight-bold">{{ Settings::get('gallery_submissions_require_approval') ? 'Votes Needed' : '' }}</div>
        <div class="col-4 col-md-2 font-weight-bold">Start</div>
        <div class="col-4 col-md-2 font-weight-bold">End</div>
    </div>
    @foreach($galleries as $gallery)
        @include('admin.galleries._galleries', ['gallery' => $gallery])
    @endforeach

    {!! $galleries->render() !!}

@endif

@endsection
