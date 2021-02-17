@extends('world.layout')

@section('title') Stats @endsection

@section('content')
{!! breadcrumbs(['Encyclopedia' => 'world', 'Stats' => 'stats']) !!}

<h1>Stats</h1>

<p>Click on the stat for more information</p>
@foreach($stats as $stat)
<div class="card mb-3">
    <div class="card-body">
        <div class="row world-entry">
             <h1 class="ml-3">{{ $stat->abbreviation }}</h1>
        </div>
        <div class="row">
            <div class="col-4 ml-3">
                <h5>Base Stat:</h5> <br> {{ $stat->default }}
                @if($stat->step)<div class="text-muted">Step: {{ $stat->step }}</div>@endif
                @if($stat->multiplier)<div class="text-muted">Multiplier: {{ $stat->multiplier }}</div>@endif
            </div>
            <div class="col-4">
                <h5>Max Level:</h5> <br> {{ $stat->max_level }}
            </div>
        </div>
    </div>
</div>
@endforeach
    
@endsection
