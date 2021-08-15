@extends('home.layout')

@section('home-title') Level Area @endsection

@section('home-content')
{!! breadcrumbs(['Level Area' => 'level-area']) !!}

<h1>
    Level Area
</h1>

<div class="container text-center">
    <h1><span class="badge badge-dark float-center text-white mx-1" data-toggle="tooltip" title="Current user level.">Current Lvl: {{ $user->level->current_level }}</span></h1>
    
    @if($next)
    <p>Next Level: {{ $next->level}}</p>
    {{ $user->level->current_exp}}/{{ $next->exp_required }}
    <div class="progress">
        <div class="progress-bar progress-bar-striped active" role="progressbar"
        aria-valuenow="{{ $user->level->current_exp}}" aria-valuemin="0" aria-valuemax="{{ $next->exp_required }}" style="width:{{$width}}%">
        {{ $user->level->current_exp}}/{{ $next->exp_required }}
        </div>
    </div>
        @if($user->level->current_exp >= $next->exp_required)
        <div class="text-center m-1">
            <b><p>You have enough EXP to advance to the nex level!</p></b>
        </div>
        {!! Form::open(['url' => 'level/up']) !!}

            {!! Form::submit('Level up!', ['class' => 'btn btn-success mb-2']) !!}

        {!! Form::close() !!}
        @endif
    @else
        <p>You are at the max level!</p>
    @endif

    <div class="mb-4 mt-2 text-center">
        <div class="card text-center">
            <div class="m-4"><strong>Current EXP:</strong> <br>{{ $user->level->current_exp }} </div>
            <div class="m-4"><strong>Current Available Stat Points:</strong> <br>{{ $user->level->current_points }}</div>
        </div>
    </div>
</div>

<div class="text-right mb-4">
    <a href="{{ url($user->url.'/level') }}">View logs...</a>
</div>
<hr>
<div class="container">
    <div class="card p-2">
        <h5>Transfer stat points to character</h5>
        <p>Note that once stat points are transferred they cannot be transferred back</p>
        {!! Form::open(['url' => 'level/transfer']) !!}
        <div class="row">
            <div class="col">
                <div class="form-group">
                    {!! Form::label('id', 'Choose Character') !!}
                    {!! Form::select('id', $characters, null, ['class'=>'form-control']) !!}
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    {!! Form::label('quantity', 'Quantity') !!}
                    {!! Form::number('quantity', null, ['class'=>'form-control']) !!}
                </div>
            </div>
        </div>
        <div class="text-right">
            {!! Form::submit('Transfer Stat Points', ['class' => 'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}
    </div>
</div>

@endsection
