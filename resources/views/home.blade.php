@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!

                    @if(Auth::user()->hasAlias)
                        <p>Your account is linked to <a href="https://www.deviantart.com/{{ Auth::user()->alias }}">{{ Auth::user()->alias }}@dA</a>.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
