@extends('home.layout')

@section('home-title') Shop Index @endsection

@section('home-content')
{!! breadcrumbs(['Home' => 'home']) !!}

<h1>
    User Shops
</h1>
<div class="text-right mb-3">
        <a class="btn btn-primary" href="{{ url('usershops/item-search') }}"><i class="fas fa-search"></i> Item Search</a>
</div>

<div>
    {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::text('name', Request::get('name'), ['class' => 'form-control']) !!}
        </div> 
        <div class="form-group mr-3 mb-3">
            {!! Form::select('sort', [
                'alpha'          => 'Sort Alphabetically (A-Z)',
                'alpha-reverse'  => 'Sort Alphabetically (Z-A)',
                'newest'         => 'Newest First',
                'oldest'         => 'Oldest First'    
            ], Request::get('sort') ? : 'category', ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
</div>
{!! $shops->render() !!}
  <div class="row ml-md-2">
    <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
      <div class="col-12 col-md-4 font-weight-bold">Name</div>
      <div class="col-4 col-md-3 font-weight-bold">Owner</div> 
    </div>
    @foreach($shops as $shop)
    <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
      <div class="col-12 col-md-4 ">{!! $shop->displayName !!}</div>
      <div class="col-4 col-md-3">{!! $shop->user->displayName !!}</div> 
    </div>
    @endforeach
  </div>
{!! $shops->render() !!}

<div class="text-center mt-4 small text-muted">{{ $shops->total() }} result{{ $shops->total() == 1 ? '' : 's' }} found.</div>

<div class="text-right mb-4">
<a href="{{ url('usershops/history') }}">View purchase logs...</a>
</div>

@endsection
