@extends('shops.layout')

@section('shops-title') My Purchase History @endsection

@section('shops-content')
{!! breadcrumbs(['Shops' => 'shops', 'My Purchase History' => 'history']) !!}

<h1>
    My Purchase History
</h1>

{!! $logs->render() !!}
<table class="table table-sm">
    <thead>
        <th>Item</th>
        <th>Shop</th>
        <th>Character</th>
        <th>Cost</th>
        <th>Date</th>
    </thead>
    <tbody>
        @foreach($logs as $log)
            @include('shops._purchase_history_row', ['log' => $log])
        @endforeach
    </tbody>
</table>
{!! $logs->render() !!}

@endsection