@extends('admin.layout')

@section('admin-title') Character Drops @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Character Drops' => 'admin/data/character-drops']) !!}

<h1>Character Drop Data</h1>

<p>Character drops are items that can be collected from characters at set intervals.</p>

<div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/data/character-drops/create') }}"><i class="fas fa-plus"></i> Create New Character Drop</a></div>
@if(!count($drops))
    <p>No character drops found.</p>
@else
    {!! $drops->render() !!}

    <div class="row ml-md-2">
    <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-bottom">
        <div class="col-6 col-md-2 font-weight-bold">Active</div>
        <div class="col-12 col-md-3 font-weight-bold">Species</div>
        <div class="col-6 col-md-2 font-weight-bold">Groups</div>
    </div>

    @foreach($drops as $drop)
        <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
        <div class="col-6 col-md-2">{!! $drop->isActive ? '<i class="text-success fas fa-check"></i>' : '' !!}</div>
        <div class="col-12 col-md-3">{!! $drop->species->displayName !!}</div>
        <div class="col-6 col-md-2">{!! implode(', ',$drop->parameterArray) !!}</div>
        <div class="col-3 col-md text-right"><a href="{{ $drop->url }}" class="btn btn-primary btn-sm py-0 px-1">Edit</a></div>
        </div>
    @endforeach

    </div>

    {!! $drops->render() !!}
    <div class="text-center mt-4 small text-muted">{{ $drops->total() }} result{{ $drops->total() == 1 ? '' : 's' }} found.</div>
@endif

@endsection
