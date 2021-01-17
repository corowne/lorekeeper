@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title') Level Area @endsection

@section('profile-content')
{!! breadcrumbs(['Level Area' => $character->url . '/level-area']) !!}

<h1>
    Level Area
</h1>

<div class="container text-center">
    <h1><span class="badge badge-dark float-center text-white mx-1" data-toggle="tooltip" title="Current Character level.">Current Lvl: {{ $character->level->current_level }}</span></h1>
    
    @if($next)
    <p>Next Level: {{ $next->level}}</p>
    {{ $character->level->current_exp}}/{{ $next->exp_required }}
    <div class="progress">
        <div class="progress-bar progress-bar-striped active" role="progressbar"
        aria-valuenow="{{ $character->level->current_exp}}" aria-valuemin="0" aria-valuemax="{{ $next->exp_required }}" style="width:{{$width}}%">
        {{ $character->level->current_exp}}/{{ $next->exp_required }}
        </div>
    </div>
        @if($character->level->current_exp >= $next->exp_required)
        <div class="text-center m-1">
            <b><p>You have enough EXP to advance to the nex level!</p></b>
        </div>
        {!! Form::open(['url' => $character->url.'/level-area/up']) !!}

            {!! Form::submit('Level up!', ['class' => 'btn btn-success mb-2']) !!}

        {!! Form::close() !!}
        @endif
    @else
        <p>You are at the max level!</p>
    @endif

    <div class="mb-4 mt-2 text-center">
        <div class="card text-center">
            <div class="m-4"><strong>Current EXP:</strong> <br>{{ $character->level->current_exp }} </div>
            <div class="m-4"><strong>Current Available Stat Points:</strong> <br>{{ $character->level->current_points }}</div>
        </div>
    </div>
</div>

<div class="text-right mb-4">
    <a href="{{ url($character->url.'/level-logs') }}">View logs...</a>
</div>

@if(Auth::check() && Auth::user()->isStaff)
<div class="alert alert-warning">
    You can see this area as a member of staff
</div>
    <h3>
        Take/Give EXP
    </h3>
    {!! Form::open(['url' => $character->url.'/level-area/exp-grant']) !!}
        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    {!! Form::label('quantity', 'Quantity') !!}
                    {!! Form::text('quantity', null, ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>
        <div class="text-right">
            {!! Form::submit('Grant', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
@endif

@endsection