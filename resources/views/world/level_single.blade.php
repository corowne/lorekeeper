@extends('world.layout')

@section('title') Levels @endsection

@section('content')
{!! breadcrumbs(['Encyclopedia' => 'world', 'Levels' => 'levels']) !!}

<div class="card mb-3">
    <div class="card-body">
        <div class="row world-entry">
            <h1 class="ml-3">Level {{ $level->level }}</h1>
        </div>
        {!! $level->description !!}
        <div class="world-entry-text">
            <div class="row">
                <div class="col-6">
                    <h4>Requirements</h4>
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
                <div class="col-6">
                <h4>Rewards</h4>
                <p>{{ $level->stat_points ? $level->stat_point : '0'}} Stat points reward </p>
                @if(!count($level->rewards))
                    No rewards.
                @else 
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th width="70%">Reward</th>
                                <th width="30%">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($level->rewards as $reward)
                                <tr>
                                    <td>{!! $reward->reward->displayName !!}</td>
                                    <td>{{ $reward->quantity }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
    
@endsection
