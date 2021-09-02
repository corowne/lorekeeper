@extends('home.layout')

@section('home-title') Bank @endsection

@section('home-content')
{!! breadcrumbs(['Bank' => 'bank']) !!}

<h1>
    Bank
</h1>

<h3>Currencies</h3>
<div class="card mb-2">
    <ul class="list-group list-group-flush">

        @foreach(Auth::user()->getCurrencies(true) as $currency)
            <li class="list-group-item">
                <div class="row">
                    <div class="col-lg-2 col-md-3 col-6 text-right">
                        <strong>
                            <a href="{{ $currency->url }}">
                                {{ $currency->name }}
                                @if($currency->abbreviation) ({{ $currency->abbreviation }}) @endif
                            </a>
                        </strong>
                    </div>
                    <div class="col-lg-10 col-md-9 col-6">
                        {{ $currency->quantity }} @if($currency->has_icon) {!! $currency->displayIcon !!} @endif
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
</div>
<div class="text-right mb-4">
    <a href="{{ url(Auth::user()->url.'/currency-logs') }}">View logs...</a>
</div>

<h3>Transfer Currency</h3>
<p>If you are transferring currency as part of a trade for on-site resources (items, currency, characters), using the <a href="{{ url('trades/open') }}">trade system</a> is recommended instead to protect yourself from being scammed.</p>
{!! Form::open(['url' => 'bank/transfer']) !!}
    <div class="form-group">
        {!! Form::label('user_id', 'Recipient') !!}
        {!! Form::select('user_id', $userOptions, null, ['class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-md-6">
                {!! Form::label('quantity', 'Quantity') !!}
                {!! Form::text('quantity', null, ['class' => 'form-control']) !!}
            </div>
            <div class="col-md-6">
                {!! Form::label('currency_id', 'Currency') !!}
                {!! Form::select('currency_id', $currencyOptions, null, ['class' => 'form-control', 'placeholder' => 'Select Currency']) !!}
            </div>
        </div>
    </div>
    <div class="text-right">
        {!! Form::submit('Transfer', ['class' => 'btn btn-primary']) !!}
    </div>
{!! Form::close() !!}

@endsection
