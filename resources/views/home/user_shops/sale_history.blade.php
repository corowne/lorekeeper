@extends('home.user_shops.layout')

@section('home.user_shops-title') My Purchase History @endsection

@section('home.user_shops-content')
{!! breadcrumbs(['User Shops' => 'user-shops/shop-index', 'My Purchase History' => 'user-shops/history']) !!}

<h1>
{!! $shop->displayName !!}'s Sale Logs
</h1>

{!! $logs->render() !!}


<div class="row ml-md-2">
  <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-bottom">
    <div class="col-12 col-md-2 font-weight-bold">Item</div>
    <div class="col-6 col-md-2 font-weight-bold">User</div>
    <div class="col-6 col-md-2 font-weight-bold">Quantity</div>
    <div class="col-6 col-md-2 font-weight-bold">Cost</div>
    <div class="col-6 col-md-2 font-weight-bold">Date</div>
  </div>
    @foreach($logs as $log)
    <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
  <div class="col-12 col-md-2">{!! $log->item ? $log->item->displayName : '(Deleted Item)' !!}</div>
  <div class="col-12 col-md-2">{!! $log->user->displayName !!}</div>
  <div class="col-12 col-md-2">{!! $log->quantity !!}</div>
  <div class="col-12 col-md-2">{!! $log->currency ? $log->currency->display($log->cost) : $log->cost . ' (Deleted Currency)' !!}</div>
  <div class="col-12 col-md-2">{!! pretty_date($log->created_at) !!}</div>
</div>
    @endforeach
</div>
{!! $logs->render() !!}

@endsection
