@extends('home.user_shops.layout')

@section('home.user_shops-title') My Purchase History @endsection

@section('home.user_shops-content')
{!! breadcrumbs(['User Shops' => 'user-shops/shop-index', 'My Purchase History' => 'user-shops/history']) !!}

<h1>
    My Purchase History
</h1>

{!! $logs->render() !!}


<div class="row ml-md-2">
  <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-bottom">
    <div class="col-12 col-md-2 font-weight-bold">Item</div>
    <div class="col-6 col-md-2 font-weight-bold">Quantity</div>
    <div class="col-6 col-md-2 font-weight-bold">Shop</div>
    <div class="col-6 col-md-2 font-weight-bold">Cost</div>
    <div class="col-6 col-md-2 font-weight-bold">Date</div>
  </div>
    @foreach($logs as $log)
        @include('home.user_shops._purchase_history_row', ['log' => $log])
    @endforeach
</div>
{!! $logs->render() !!}

@endsection
