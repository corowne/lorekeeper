@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title') Stat Area @endsection

@section('profile-content')
{!! breadcrumbs(['Stat Area' => $character->url . '/stats-area']) !!}

<h1>
    Stat Area
</h1>

<div class="container text-center">
    <div class="m-4"><strong>Current Available Stat Points:</strong> <br>{{ $character->level->current_points }}</div>
    <div class="row">
        @foreach($character->stats as $stat)
            <div class="col p-1" style="border-style: solid; border-width: .1em; border-color:#ddd;">
                <h5>{{$stat->stat->name}}</h5>
                <p><strong>Current {{$stat->stat->name}} Level:</strong></p>
                <p>{{$stat->stat_level}}</p>
                <p><strong>Total {{$stat->stat->name}} Count:</strong></p>
                <p>{{$stat->count}}</p>
                @if($stat->current_count != NULL)
                <p><strong>Current {{$stat->stat->name}} Count:</strong></p>
                <p>{{$stat->current_count}}/{{$stat->count}}</p>
                @endif
                @if($character->level->current_points > 0)
                {{ Form::open(['url' => $character->url . '/stats-area/' . $stat->id]) }}
                
                {!! Form::submit('Level Stat!', ['class' => 'btn btn-success mb-2']) !!}

                {!! Form::close() !!}
                @endif
            </div>
        @endforeach
    </div>
</div>

<div class="text-right mb-4">
    <a href="{{ url($character->url.'/level') }}">View logs...</a>
</div>
@endsection
