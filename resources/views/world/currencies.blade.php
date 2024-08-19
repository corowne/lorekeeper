@extends('world.layout')

@section('world-title')
    Currencies
@endsection

@section('content')
    {!! breadcrumbs(['World' => 'world', 'Currencies' => 'world/currencies']) !!}
    <h1>Currencies</h1>

    <div>
        {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::text('name', Request::get('name'), ['class' => 'form-control']) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}
    </div>

    {!! $currencies->render() !!}
    @foreach ($currencies as $currency)
        <div class="card mb-3">
            <div class="card-body">
                @include('world._currency_entry', ['currency' => $currency])
            </div>
        </div>
    @endforeach
    {!! $currencies->render() !!}

    <div class="text-center mt-4 small text-muted">{{ $currencies->total() }} result{{ $currencies->total() == 1 ? '' : 's' }} found.</div>
@endsection
