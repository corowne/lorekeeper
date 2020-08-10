@extends('admin.layout')

@section('admin-title') Scavenger Hunts @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Scavenger Hunts' => 'admin/data/hunts']) !!}

<h1>Scavenger Hunts</h1>

<p>This is a list of scavenger hunts.</p> 

<div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/data/hunts/create') }}"><i class="fas fa-plus"></i> Create New Hunt</a></div>

@if(!count($hunts))
    <p>No hunts found.</p>
@else 
    {!! $hunts->render() !!}

    <div class="row ml-md-2">
      <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
        <div class="col-4 col-md-1 font-weight-bold">Active</div>
        <div class="col-4 col-md-3 font-weight-bold">Name</div>
        <div class="col-4 col-md-3 font-weight-bold">Display Name</div>
        <div class="col-4 col-md-2 font-weight-bold">Start</div>
        <div class="col-4 col-md-2 font-weight-bold">End</div>
      </div>
      @foreach($hunts as $hunt)
      <div class="d-flex row flex-wrap col-12 mt-1 pt-2 px-0 ubt-top">
        <div class="col-2 col-md-1">
          {!! $hunt->isActive ? '<i class="text-success fas fa-check"></i>' : '' !!}
        </div>
        <div class="col-5 col-md-3 text-truncate">
          {{ $hunt->name }}
        </div>
        <div class="col-5 col-md-3">
          {!! $hunt->displayLink !!}
        </div>
        <div class="col-4 col-md-2">
          {!! pretty_date($hunt->start_at) !!}
        </div>
        <div class="col-4 col-md-2">
          {!! pretty_date($hunt->end_at) !!}
        </div>
        <div class="col-3 col-md-1 text-right">
          <a href="{{ url('admin/data/hunts/edit/'.$hunt->id) }}"  class="btn btn-primary py-0 px-2">Edit</a>
        </div>
      </div>
      @endforeach
    </div>

    {!! $hunts->render() !!}
@endif

@endsection

@section('scripts')
@parent
@endsection