@extends('shops.layout')

@section('shops-title')
    My Purchase History
@endsection

@section('shops-content')
    {!! breadcrumbs(['Shops' => 'shops', 'My Purchase History' => 'history']) !!}

    <h1>
        My Purchase History
    </h1>

    {!! $logs->render() !!}

    <div class="mb-4 logs-table">
        <div class="logs-table-header">
            <div class="row">
                <div class="col-12 col-md-2">
                    <div class="logs-table-cell">Item</div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="logs-table-cell">Quantity</div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="logs-table-cell">Shop</div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="logs-table-cell">Character</div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="logs-table-cell">Cost</div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="logs-table-cell">Date</div>
                </div>
            </div>
        </div>
        <div class="logs-table-body">
            @foreach ($logs as $log)
                <div class="logs-table-row">
                    @include('shops._purchase_history_row', ['log' => $log])
                </div>
            @endforeach
        </div>
    </div>
    {!! $logs->render() !!}
@endsection
