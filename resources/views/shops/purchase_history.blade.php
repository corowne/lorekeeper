@extends('shops.layout')

@section('shops-title') My Purchase History @endsection

@section('shops-content')
{!! breadcrumbs(['Shops' => 'shops', 'My Purchase History' => 'history']) !!}

<h1>
    My Purchase History
</h1>

{!! $logs->render() !!}


<div class="row ml-md-2">
  <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-bottom">
    <div class="col-12 col-md-2 font-weight-bold">Item</div>
    <div class="col-6 col-md-2 font-weight-bold">Quantity</div>
    <div class="col-6 col-md-2 font-weight-bold">Shop</div>
    <div class="col-6 col-md-2 font-weight-bold">Character</div>
    <div class="col-6 col-md-2 font-weight-bold">Cost</div>
    <div class="col-6 col-md-2 font-weight-bold">Date</div>
  </div>
    @foreach($logs as $log)
        @include('shops._purchase_history_row', ['log' => $log])
    @endforeach
</div>
{!! $logs->render() !!}

@endsection
