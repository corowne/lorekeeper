@extends('character.layout')

@section('profile-title') {{ $character->fullName }}'s Bank @endsection

@section('profile-content')
{!! breadcrumbs(['Masterlist' => 'masterlist', $character->fullName => $character->url, $character->fullName."'s Bank" => $character->url.'/bank']) !!}

@include('character._header', ['character' => $character])

<h3>Currencies</h3>
<div class="card mb-4">
    <ul class="list-group list-group-flush">
    
        @foreach($character->getCurrencies(false) as $currency)
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

<h3>Latest Activity</h3>
<table class="table table-sm">
    <thead>
        <th>Sender</th>
        <th>Recipient</th>
        <th>Currency</th>
        <th>Log</th>
        <th>Date</th>
    </thead>
    <tbody>
        @foreach($logs as $log)
            {!! $log->displayRow($character) !!}
        @endforeach
    </tbody>
</table>
<div class="text-right">
    <a href="{{ url($character->url.'/currency-logs') }}">View all...</a>
</div>

@endsection
