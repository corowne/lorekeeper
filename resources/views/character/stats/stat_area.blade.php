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
                @if($stat->current_count != $stat->count && $stat->current_count != NULL)
                <p><strong>Current {{$stat->stat->name}} Count:</strong></p>
                <p>{{$stat->current_count}}/{{$stat->count}}</p>
                @endif
                @if($character->level->current_points > 0)
                {{ Form::open(['url' => $character->url . '/stats-area/' . $stat->id]) }}
                
                {!! Form::submit('Level Stat!', ['class' => 'btn btn-success mb-2']) !!}

                {!! Form::close() !!}
                @endif
                @if(Auth::check() && Auth::user()->isStaff)
                <div class="container text-center">
                    <p class="badge badge-warning">
                        Admin Area
                    </p>
                    <div class="row">
                        <div class="col">
                            {{ Form::open(['url' => $character->url . '/stats-area/edit/' . $stat->id]) }}
                    
                            {!! Form::number('count', $stat->current_count, ['class' => 'form-control m-1']) !!}
                            {!! add_help('Here you can decrement or increment the current stat count for a character') !!}
                            {!! Form::submit('Edit Current Count', ['class' => 'btn btn-sm btn-primary mb-2']) !!} 
            
                            {!! Form::close() !!}
                        </div>
                        <div class="col">
                            {{ Form::open(['url' => $character->url . '/stats-area/admin/' . $stat->id]) }}
                    
                            {!! add_help('This does not debit stat points') !!}
                            {!! Form::submit('Admin Level Up', ['class' => 'btn btn-sm btn-success mb-2']) !!} 
            
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        @endforeach
    </div>
</div>

<div class="text-right mt-2 mb-4">
    <a href="{{ url($character->url.'/level-logs') }}">View logs...</a>
</div>

@if(Auth::check() && Auth::user()->isStaff)
<div class="alert alert-warning">
    You can see this area as a member of staff
</div>
    <h3>
        Take/Give Stat Points
    </h3>
    {!! Form::open(['url' => $character->url.'/level-area/stat-grant']) !!}
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
