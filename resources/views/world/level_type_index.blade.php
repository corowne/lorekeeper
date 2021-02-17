@extends('world.layout')

@section('title') Levels @endsection

@section('content')
{!! breadcrumbs(['Encyclopedia' => 'world', 'Levels' => 'levels']) !!}

<h1>{{ $type }} Levels</h1>

<p>Click on the level for more information</p>
<div class="card mb-3">
    <div class="card-body">
        <div class="row world-entry">
            <h1 class="ml-3">Level 1<h2>
        </div>
        <p>The beginner level!</p>
    </div>
</div>
@foreach($levels as $level)
<div class="card mb-3">
    <div class="card-body">
        <div class="row world-entry">
             <a href="{{ $type }}/{{ $level->level }}"><h1 class="ml-3">Level {{ $level->level }}</h1></a>
        </div>
        {!! $level->description !!}
        @if($level->limits->count())
        <div class="text-danger">Requires:
            <?php 
            $limits = []; 
            foreach($level->limits as $limit)
            {
            $name = $limit->reward->name;
            $limits[] = $name;
            }
            echo implode(', ', $limits);
            ?>
        </div>
        @else <p>No requirements.</p>
        @endif
    </div>
</div>
@endforeach
    
@endsection
