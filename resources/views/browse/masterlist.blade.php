@extends('layouts.app')

@section('title') Character Masterlist @endsection

@section('content')
{!! breadcrumbs(['Character Masterlist' => 'masterlist']) !!}
<h1>Character Masterlist</h1>

<div>
    {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::text('name', Request::get('name'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mr-3 mb-3">
            {!! Form::select('rarity_id', $rarities, Request::get('rarity_id'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
</div>

{!! $characters->render() !!}
@foreach($characters->chunk(4) as $chunk)
    <div class="row">
        @foreach($chunk as $character)
        <div class="col-md-3 col-6 text-center">
            <div>
                <a href="{{ $character->url }}"><img src="{{ $character->image->thumbnailUrl }}" class="img-thumbnail" /></a>
            </div>
            <div class="mt-1">
                <a href="{{ $character->url }}" class="h5 mb-0">{{ $character->fullName }}</a>
            </div>
        </div>
        @endforeach
    </div>
@endforeach
{!! $characters->render() !!}

<div class="text-center mt-4 small text-muted">{{ $characters->total() }} result{{ $characters->total() == 1 ? '' : 's' }} found.</div>

@endsection
