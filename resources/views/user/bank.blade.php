@extends('user.layout')

@section('profile-title') {{ $user->name }}'s Bank @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Bank' => $user->url . '/bank']) !!}

<h1>
    {!! $user->displayName !!}'s Bank
</h1>

<h3>Currencies</h3>
<div class="card mb-4">
    <ul class="list-group list-group-flush">

        @foreach($user->getCurrencies(true) as $currency)
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
<div class="row ml-md-2 mb-4">
  <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-bottom">
    <div class="col-6 col-md-2 font-weight-bold">Sender</div>
    <div class="col-6 col-md-2 font-weight-bold">Recipient</div>
    <div class="col-6 col-md-2 font-weight-bold">Currency</div>
    <div class="col-6 col-md-4 font-weight-bold">Log</div>
    <div class="col-6 col-md-2 font-weight-bold">Date</div>
  </div>
  @foreach($logs as $log)
      @include('user._currency_log_row', ['log' => $log, 'owner' => $user])
  @endforeach
</div>
<div class="text-right">
    <a href="{{ url($user->url.'/currency-logs') }}">View all...</a>
</div>

@endsection
